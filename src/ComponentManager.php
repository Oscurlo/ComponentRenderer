<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;

class ComponentManager extends ComponentInterface
{
    # DOM
    protected DOMDocument $dom;

    # DOM version
    public string $dom_version = "1.0";

    # DOM encoding
    public string $dom_encoding = "UTF-8";

    # Component path
    protected static array $component_manager = [];

    # Validates if it contains the html tag and does not need to load a base content
    # I created this variable, for example, to load layouts.
    protected bool $contains_html_base = false;
    private array $check_storage;

    /**
     * Set the component path
     *
     * @param  array $components
     * @return void
     */

    public function set_component_manager(array $components): void
    {
        $array = [...$components, ...self::$component_manager];
        $components = array_map(
            fn (array|string $name) => is_string($name) ? [$name] : $name,
            $array
        );

        foreach ($components as $references => $values) {
            foreach ($values as $i => $component) {
                $old_key = $references;
                $new_key = str_replace(["/", "\\"], DIRECTORY_SEPARATOR, $references);

                if (!self::component_exists($old_key, $component)) {
                    $new_key = realpath($new_key);
                    unset($components[$old_key][$i], $components[$new_key][$i]);
                }
            }
        }

        self::$component_manager = [...$components, ...self::$component_manager];
    }

    /**
     * Get the component path
     *
     * @return array|null
     */
    public function get_component_manager(): ?array
    {
        return self::$component_manager;
    }

    /**
     * @param  array     $components
     * @return void
     * @throws Exception
     */
    public static function register_component(array $components): void
    {
        (new static())->set_component_manager($components);
    }

    /**
     * Check if the component exists
     *
     * @param  string $folder_or_function
     * @param  string $component          Component name
     * @return bool
     */
    protected function component_exists(string $folder_or_function, string $component): bool
    {
        return match (is_dir($folder_or_function)) {
            true => self::check("file", $folder_or_function, $component),
            false => self::check("method", $folder_or_function, $component) || self::check("function", $folder_or_function, $component),
            default => false
        };
    }

    /**
     * @param  string $folder
     * @param  string $component
     * @return string
     */
    protected static function get_file(string $folder, string $component): string
    {
        [$file] = explode("::", $component);
        return "{$folder}/{$file}.php";
    }

    protected function convert_to_valid_tag(string &$html): void
    {
        $array_tag = fn ($tag) => ["<{$tag}", "</{$tag}"];

        foreach (self::$component_manager as $folder => $components) {
            foreach ($components as $component) {
                if (self::check("method", $folder, $component)) {
                    $split = explode("\\", $folder);
                    $class = end($split);
                    $component = "{$class}::{$component}";
                }

                $html = str_replace(
                    $array_tag($component),
                    $array_tag(self::valid_tag($folder, $component)),
                    $html
                );
            }
        }
    }

    /**
     * @param  string $folder_or_function
     * @param  string $component
     * @return string
     */
    protected function valid_tag(string $folder_or_function, string $component): string
    {
        $name = "component-";

        if (strpos($component, $name)) {
            return $component;
        }

        if (self::check("method", $folder_or_function, $component)) {
            $split = explode("\\", $folder_or_function);
            $class = end($split);
            $component = "{$class}::{$component}";
        }

        $name .= strtolower(str_replace("::", "-", $component));

        return $name;
    }

    /**
     * @param  string $exists
     * @param  string $folder_or_function
     * @param  string $component
     * @return bool
     */
    protected function check(string $exists, string $folder_or_function, string $component): bool
    {
        $key = "{$exists}->{$folder_or_function}[$component]";

        if (isset($this->check_storage[$key])) {
            return $this->check_storage[$key];
        }

        $split = explode("::", $component);
        $class = $split[0] ?? "";
        $method = $split[1] ?? "";

        $this->check_storage[$key] = match ($exists) {
            # file
            "file" => file_exists(self::get_file($folder_or_function, $component)),
            # namespace
            "method" => method_exists($folder_or_function, $component),
            "function" => function_exists(self::valid_name_function($folder_or_function, $component)),
            # path
            "method_normal" => method_exists($class, $method),
            "function_normal" => function_exists($component)
        };

        return $this->check_storage[$key];
    }

    /**
     * @param  string $folder_or_function
     * @param  string $component
     * @return string
     */
    protected static function valid_name_function(string $folder_or_function, string $component): string
    {
        return "{$folder_or_function}\\{$component}";
    }

    /**
     * Extract attributes from an array
     *
     * @deprecated I didn't find the feature very useful, and I'm not sure if I should remove it. use "self::get_attributes"
     * @param  array|object $main     Main array
     * @param  array        $columns  Columns to extract
     * @param  string       $encoding Encoding type
     * @return string
     */
    public static function extract_attributes(array|object $main, array $columns, string $encoding = "UTF-8"): string
    {
        $attrs = [];

        foreach ((array) $main as $key => $value) {
            if (in_array($key, $columns)) {
                $attrs[] = "{$key}=\"" . htmlspecialchars((string) $value, ENT_QUOTES, $encoding) . "\"";
            }
        }

        return implode(" ", $attrs);
    }

    /**
     *
     * @param  array|object $main    Main array
     * @param  array        $exclude Includes ["children", "textContent"]
     * @return string
     */
    public static function get_attributes(array|object $main, array $exclude = []): string
    {
        $main = (array) $main;

        return self::extract_attributes(
            $main,
            array_filter(
                array_keys(array_filter($main, fn ($value): bool => !empty($value))),
                fn ($key): bool => !in_array($key, ["children", "textContent", ...$exclude])
            )
        );
    }

    /**
     * Generate a base HTML structure
     *
     * @param  string $html     HTML content
     * @param  string $encoding Encoding type
     * @return string
     */
    public static function html_base(string $html, string $encoding = "UTF-8"): string
    {
        return <<<HTML
        <html lang="en">
            <meta http-equiv="Content-Type" content="text/html;charset={$encoding}">
            <meta charset="{$encoding}">
            <body>{$html}</body>
        </html>
        HTML;
    }

    /**
     * Get the contents inside the body tag
     *
     * @param  string $html     HTML content
     * @param  string $version  DOM version
     * @param  string $encoding DOM encoding
     * @return string
     */
    public static function get_body(string $html, string $version = "1.0", string $encoding = "UTF-8"): string
    {
        $dom = new DOMDocument($version, $encoding);
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $bodyNode = $xpath->query('//body')->item(0);
        $bodyContent = "";

        if ($bodyNode) {
            foreach ($bodyNode->childNodes as $child) {
                $bodyContent .= $dom->saveHTML($child);
            }
        }

        $bodyContent ??= $html;

        return $bodyContent;
    }

    /**
     * Gets the attributes of the component
     *
     * @param  DOMNode $tag
     * @return object
     */
    protected function get_params(DOMNode $tag): object
    {
        $attrs = [];
        $attrs["children"] = "";

        foreach ($tag->attributes as $attr) {
            $attrs[$attr->nodeName] = $attr->nodeValue;
        }

        if (!empty($tag->childNodes->count())) {
            $attrs["children"] = "";

            foreach ($tag->childNodes as $child) {
                $attrs["children"] .= $this->dom->saveHTML(
                    $this->dom->importNode(
                        $child,
                        true
                    )
                );
            }
        }

        $attrs["textContent"] = $tag->textContent;

        return (object) $attrs;
    }

    /**
     * Validate if it contains html
     *
     * @param  string $html
     * @return false
     */
    protected static function contains_html_base(string $html): bool
    {
        return (bool) preg_match("|<html(.*?)</html>|s", $html);
    }

    /**
     * Print 😅
     * @param string ...$expressions
     */
    public static function print(string ...$expressions): void
    {
        echo  implode(" ", $expressions);
    }
}
