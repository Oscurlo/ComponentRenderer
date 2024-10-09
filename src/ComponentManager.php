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
    public DOMDocument $dom;

    # DOM version
    public string $dom_version = "1.0";

    # DOM encoding
    public string $dom_encoding = "UTF-8";

    # Component path
    protected array $component_folders = [];

    # Validates if it contains the html tag and does not need to load a base content
    # I created this variable, for example, to load layouts.
    protected bool $contains_html_base = false;

    /**
     * Set the component path
     * 
     * @param string $source Path to the component
     * @throws Exception If the folder is not found
     * @return void
     */

    public function __construct()
    {
        $this->dom = new DOMDocument(
            $this->dom_version,
            $this->dom_encoding
        );

        # https://www.php.net/manual/en/class.domdocument.php
        $this->dom->preserveWhiteSpace = false; # Remove redundant white space
        $this->dom->formatOutput = true; # Output formats with indentation and extra space.
    }

    public function set_component_manager(array $components): void
    {
        $this->component_folders = array_filter(
            array_map(
                fn(string $key, array|string $value) => [
                    rtrim($key, "/\\") => array_map(
                        fn($value) => trim($value),
                        array_unique(is_string($value) ? [$value] : $value)
                    )
                ],
                array_keys($components),
                array_values($components)
            ),
            fn($component) => !empty ($component)
        );
    }

    /**
     * Get the component path
     * 
     * @return array|null
     */
    public function get_component_manager()
    {
        return $this->component_folders;
    }

    /**
     * Check if the component exists
     * 
     * @param string $component Component name
     * @return bool
     */
    protected static function component_exists(string $folder_or_function, string $component): bool
    {
        return match (is_dir($folder_or_function)) {
            true => self::check("file", $folder_or_function, $component),
            false => self::check("method", $folder_or_function, $component) || self::check("function", $folder_or_function, $component),
            default => false
        };
    }

    protected static function get_file($folder, $component): string
    {
        [$file] = explode("::", $component);
        return "{$folder}/{$file}.php";
    }

    /**
     * Encode an array to JSON
     * 
     * @param array $value Array to encode
     * @return bool|string
     */
    static function json_encode(array $value): bool|string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    protected function convert_to_valid_tag(string &$html): void
    {
        $array_tag = fn($tag) => ["<{$tag}", "</{$tag}"];

        foreach ($this->component_folders as $a => $folders) {
            foreach ($folders as $folder => $components) {
                foreach ($components as $e => $component) {
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
    }

    protected static function valid_tag(string $folder_or_function, string $component): string
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

    protected static function check(string $exists, string $folder_or_function, string $component): bool
    {
        @[$class, $method] = explode("::", $component);

        return match ($exists) {
            # file
            "file" => file_exists(self::get_file($folder_or_function, $component)),
            # namespace
            "method" => method_exists($folder_or_function, $component),
            "function" => function_exists(self::valid_name_function($folder_or_function, $component)),
            # path
            "method_normal" => method_exists((string) $class, (string) $method),
            "function_normal" => function_exists($component),
        };
    }

    protected static function valid_name_function(string $folder_or_function, string $component)
    {
        return str_ireplace("function ", "", "{$folder_or_function}\\{$component}");
    }

    /**
     * Extract attributes from an array
     * 
     * @deprecated I didn't find the feature very useful and I'm not sure if I should remove it. use "self::get_attributes"
     * @param array|object $main Main array
     * @param array $columns Columns to extract
     * @param string $encoding Encoding type
     * @return string
     */
    static function extract_attributes(array|object $main, array $columns, string $encoding = "UTF-8"): string
    {
        $attrs = [];

        foreach ((array) $main as $key => $value) {
            if (in_array($key, $columns)) {
                $attrs[] = "{$key}=\"" . htmlspecialchars($value, ENT_QUOTES, $encoding) . "\"";
            }
        }

        return implode(" ", $attrs);
    }

    /**
     * 
     * @param array|object $main Main array
     * @param array $exclude Includes ["children", "textContent"]
     * @return string
     */
    static function get_attributes(array|object $main, array $exclude = []): string
    {
        $main = (array) $main;

        return self::extract_attributes(
            $main,
            array_filter(
                array_keys(array_filter($main, fn($value): bool => !empty ($value))),
                fn($key): bool => !in_array($key, ["children", "textContent", ...$exclude])
            )
        );
    }

    /**
     * Generate a base HTML structure
     * 
     * @param string $html HTML content
     * @param string $encoding Encoding type
     * @return string
     */
    static function html_base(string $html, string $encoding = "UTF-8"): string
    {
        return <<<HTML
        <html>
            <meta http-equiv="Content-Type" content="text/html;charset={$encoding}">
            <meta charset="{$encoding}">
            <body>{$html}</body>
        </html>
        HTML;
    }

    /**
     * Get the contents inside the body tag
     * 
     * @param string $html HTML content
     * @param string $version DOM version
     * @param string $encoding DOM encoding
     * @return string
     */
    static function get_body(string $html, string $version = "1.0", string $encoding = "UTF-8"): string
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

        return $bodyContent ?? $html;
    }

    /**
     * Gets the attributes of the component
     * 
     * @param DOMNode $tag
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
                $attrs["children"] .= $this->dom->saveHTML($this->dom->importNode($child, true));
            }
        }

        $attrs["textContent"] = $tag->textContent;

        return (object) $attrs;
    }

    /**
     * Validate if it contains html
     * 
     * @param string $html
     * @return false
     */
    protected static function contains_html_base(string $html): bool
    {
        return preg_match("|<html(.*?)</html>|s", $html) ? true : false;
    }

    /**
     * Print 😅
     */
    protected static function print(string ...$expressions): void
    {
        echo implode(" ", $expressions);
    }
}
