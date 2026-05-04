<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use Exception;

class ComponentRegistry
{
    /**
     * Registered components map: reference => [component, ...]
     *
     * @var array<string, array<int, string>>
     */
    protected array $component_manager = [];

    /**
     * Whether the current HTML being processed is a full document (has <html> tag).
     * Used to decide whether to wrap/unwrap with HtmlHelper.
     */
    protected bool $contains_html_base = false;

    /**
     * Internal cache for component existence checks to avoid redundant lookups.
     *
     * @var array<string, bool>
     */
    private array $check_storage = [];

    // -------------------------------------------------------------------------
    // Registration
    // -------------------------------------------------------------------------

    /**
     * Register one or more components into the manager.
     * Each entry maps a reference (namespace, class, or directory path)
     * to a component name or list of names.
     *
     * @param  array<string, string|array<int, string>> $components
     * @return void
     */
    public function set_component_manager(array $components): void
    {
        $merged = [...$components, ...$this->component_manager];

        $normalized = array_map(
            fn(array|string $name) => is_string($name) ? [$name] : $name,
            $merged,
        );

        foreach ($normalized as $references => $values) {
            foreach ($values as $i => $component) {
                $old_key = $references;
                $new_key = str_replace(
                    ["/", "\\"],
                    DIRECTORY_SEPARATOR,
                    $references,
                );

                if (!$this->component_exists($old_key, $component)) {
                    $new_key = realpath($new_key);
                    unset($normalized[$old_key][$i], $normalized[$new_key][$i]);
                }
            }
        }

        $this->component_manager = [
            ...$normalized,
            ...$this->component_manager,
        ];
    }

    /**
     * Return the current component map, or null if empty.
     *
     * @return array<string, array<int, string>>|null
     */
    public function get_component_manager(): ?array
    {
        return $this->component_manager ?: null;
    }

    // -------------------------------------------------------------------------
    // Resolution
    // -------------------------------------------------------------------------

    /**
     * Check whether a component reference + name combination actually exists
     * (as a file, namespaced function, class method, etc.).
     *
     * @param  string $folder_or_function
     * @param  string $component
     * @return bool
     */
    protected function component_exists(
        string $folder_or_function,
        string $component,
    ): bool {
        return match (is_dir($folder_or_function)) {
            true => $this->check("file", $folder_or_function, $component),
            false => $this->check("method", $folder_or_function, $component) ||
                $this->check("function", $folder_or_function, $component),
            default => false,
        };
    }

    /**
     * Resolve the file path for a directory-based component.
     *
     * @param  string $folder
     * @param  string $component
     * @return string
     */
    protected static function get_file(
        string $folder,
        string $component,
    ): string {
        [$file] = explode("::", $component);
        return "{$folder}/{$file}.php";
    }

    /**
     * Convert custom component tags in the HTML string to valid lowercased
     * HTML tag names that DOMDocument can parse (e.g. "MyTag" → "component-mytag").
     *
     * @param  string $html
     * @return void
     */
    protected function convert_to_valid_tag(string &$html): void
    {
        $array_tag = fn(string $tag): array => ["<{$tag}", "</{$tag}"];

        foreach ($this->component_manager as $folder => $components) {
            foreach ($components as $component) {
                if ($this->check("method", $folder, $component)) {
                    $split = explode("\\", $folder);
                    $class = end($split);
                    $component = "{$class}::{$component}";
                }

                $html = str_replace(
                    $array_tag($component),
                    $array_tag($this->valid_tag($folder, $component)),
                    $html,
                );
            }
        }
    }

    /**
     * Produce a safe, lowercased HTML tag name for a given component reference.
     * Already-prefixed tags ("component-*") are returned as-is.
     *
     * @param  string $folder_or_function
     * @param  string $component
     * @return string
     */
    protected function valid_tag(
        string $folder_or_function,
        string $component,
    ): string {
        $prefix = "component-";

        if (str_contains($component, $prefix)) {
            return $component;
        }

        if ($this->check("method", $folder_or_function, $component)) {
            $split = explode("\\", $folder_or_function);
            $class = end($split);
            $component = "{$class}::{$component}";
        }

        return $prefix . strtolower(str_replace("::", "-", $component));
    }

    /**
     * Unified existence check for files, methods, and functions.
     * Results are memoized in $check_storage for the lifetime of the instance.
     *
     * @param  string $exists            One of: file|method|function|method_normal|function_normal
     * @param  string $folder_or_function
     * @param  string $component
     * @return bool
     */
    protected function check(
        string $exists,
        string $folder_or_function,
        string $component,
    ): bool {
        $key = "{$exists}->{$folder_or_function}[{$component}]";

        if (isset($this->check_storage[$key])) {
            return $this->check_storage[$key];
        }

        $split = explode("::", $component);
        $class = $split[0] ?? "";
        $method = $split[1] ?? "";

        $this->check_storage[$key] = match ($exists) {
            "file" => file_exists(
                static::get_file($folder_or_function, $component),
            ),
            "method" => method_exists($folder_or_function, $component),
            "function" => function_exists(
                static::valid_name_function($folder_or_function, $component),
            ),
            "method_normal" => method_exists($class, $method),
            "function_normal" => function_exists($component),
        };

        return $this->check_storage[$key];
    }

    /**
     * Build the fully-qualified function name for a namespaced function component.
     *
     * @param  string $folder_or_function
     * @param  string $component
     * @return string
     */
    protected static function valid_name_function(
        string $folder_or_function,
        string $component,
    ): string {
        return "{$folder_or_function}\\{$component}";
    }
}
