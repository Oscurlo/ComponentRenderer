<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Exception;

class ComponentManager
{
    /**
     * DOM version
     */
    public string $dom_version = "1.0";

    /**
     * DOM encoding
     */
    public string $dom_encoding = "UTF-8";

    /**
     * Component path
     */
    protected ?array $component_folders = null;

    /**
     * Valid random tag for html
     */
    protected string $tag = "object";

    protected bool $contains_html = false;

    /**
     * Set the component path
     * 
     * @param string $source Path to the component
     * @throws Exception If the folder is not found
     * @return void
     */
    public function set_component_manager(array $folders): void
    {
        # { "folder": ["comp1", "comp2"] }
        $this->component_folders = array_filter(
            array_map(
                fn(string $keys, string|array $values) => [
                    rtrim(trim($keys), "/\\") => array_map(
                        fn($val) => trim($val),
                        array_unique(
                            is_string($values) ? [$values] : $values
                        )
                    )
                ],
                array_keys($folders),
                array_values($folders)
            ),
            fn(array $components) => !empty($components)
        );
    }

    /**
     * Get the component path
     * 
     * @return array|null
     */
    public function get_component_path(): ?array
    {
        return $this->component_folders;
    }

    /**
     * Replace the component with a valid HTML tag
     * 
     * @param string $tagFrom
     * @param string $tagTo
     * @param string $html
     * @return string
     */
    protected static function replace_tag(string $tagFrom, string $tagTo, string $html): string
    {
        return str_ireplace(["<{$tagFrom}", "{$tagFrom}>"], ["<{$tagTo}", "{$tagTo}>"], $html);
    }

    /**
     * Check if the component exists
     * 
     * @param string $component Component name
     * @return bool
     */
    protected function component_exists(string $folder, string $component): bool
    {
        return file_exists(self::get_file($folder, $component));
    }

    protected function get_file(string $folder, string $component): string
    {
        # Debo re-hacer las funciones en la use "[explode -> ::]"
        [$file] = explode("::", $component);

        return "{$folder}/{$file}.php";
    }

    /**
     * Split the component into folder and component name
     * 
     * @param string $component Component name
     * @return array
     */
    protected function split_component(string $component): array
    {
        [$dirname, $basename] = [dirname($component), basename($component)];
        $splt = explode("::", $basename);
        $function = $splt[0] ?? null;
        $method = $splt[1] ?? null;

        return [
            "folder" => $dirname === "." ? null : str_replace("./", "", $dirname),
            "component" => $function,
            "method" => $method
        ];
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

    public function convert_to_valid_tag(string $html): string
    {
        $oc = fn($tag) => ["<{$tag}", "</{$tag}"];
        if ($this->component_folders) foreach ($this->component_folders as $folders) {
            foreach ($folders as $folder => $components) {
                foreach ($components as $component) {
                    $html = str_replace($oc($component), $oc(self::valid_tag($component)), $html);
                }
            }
        }

        return $html;
    }

    public function valid_tag(string $component): string
    {
        $n = "component-";
        $n .= strtolower(str_replace(["::", $n], ["-", ""], $component));

        return $n;
    }

    /**
     * Extract attributes from an array
     * 
     * @param array $main Main array
     * @param array $columns Columns to extract
     * @param string $encoding Encoding type
     * @return string
     */
    static function extract_attributes(array $main, array $columns, string $encoding = "UTF-8"): string
    {
        $attrs = [];
        foreach ($main as $key => $value) {
            if (in_array($key, $columns)) {
                $attrs[] = "{$key}=\"" . htmlspecialchars($value, ENT_QUOTES, $encoding) . "\"";
            }
        }
        return implode(" ", $attrs);
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
     * @param DOMDocument $doms
     */
    protected function get_params(DOMNode $tag, DOMDocument $dom): object
    {
        $attrs = [];
        $attrs["children"] = "";

        foreach ($tag->attributes as $attr) $attrs[$attr->nodeName] = $attr->nodeValue;

        if (!empty($tag->childNodes->count())) {
            $attrs["children"] = "";

            foreach ($tag->childNodes as $child) $attrs["children"] .= $dom->saveHTML($dom->importNode($child, true));
        }

        $attrs["textContent"] = $tag->textContent;

        return (object)$attrs;
    }

    /**
     * 
     */
    protected function contains_html_base(string $html): bool
    {
        return preg_match("|<html(.*?)</html>|s", $html) ? true : false;
    }
}
