<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use DOMDocument;
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
    protected ?string $component_path = null;

    /**
     * Indicates if the component path is defined
     */
    protected bool $component_path_is_defined = false;

    /**
     * Set the component path
     * 
     * @param string $source Path to the component
     * @throws Exception If the folder is not found
     * @return void
     */
    public function set_component_path(string $source): void
    {
        if (!is_dir($source)) {
            throw new Exception("We couldn't find the folder");
        }

        $this->component_path_is_defined = true;
        $this->component_path = rtrim($source, "/\\");
    }

    /**
     * Get the component path
     * 
     * @return string|null
     */
    public function get_component_path(): ?string
    {
        return $this->component_path;
    }

    /**
     * Check if the component exists
     * 
     * @param string $component Component name
     * @return bool
     */
    protected function component_exists(string $component): bool
    {
        return file_exists($this->get_component_file($component));
    }

    /**
     * Get the component file path
     * 
     * @param string $component Component name
     * @return string
     */
    protected function get_component_file(string $component): string
    {
        ["folder" => $folder, "component" => $file] = self::split_component($component);
        $path = $this->get_component_path();

        return $folder ? "{$path}/{$folder}/{$file}.php" : "{$path}/{$file}.php";
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

    /**
     * Comment out HTML components
     * 
     * @param string $html HTML content
     * @param array $components Components to comment out
     * @return string
     */
    protected function comment_component(string $html, array $components): string
    {
        return self::comment_or_uncomment("comment", $html, $components);
    }

    /**
     * Uncomment HTML components
     * 
     * @param string $html HTML content
     * @param array $components Components to uncomment
     * @return string
     */
    protected function uncomment_component(string $html, array $components): string
    {
        return self::comment_or_uncomment("uncomment", $html, $components);
    }

    /**
     * Comment or uncomment HTML components
     * 
     * @param string $action Action to perform ("comment" or "uncomment")
     * @param string $html HTML content
     * @param array $components Components to process
     * @return string
     */
    private function comment_or_uncomment(string $action, string $html, array $components): string
    {
        $comment = fn($tag) => ["<!-- <{$tag}", "</{$tag}> -->"];
        $uncomment = fn($tag) => ["<{$tag}", "</{$tag}>"];

        foreach ($components as $component) {
            if ($action === "comment" && strpos($html, $comment($component)[0]) !== false) continue;

            $html = str_replace(
                ...[
                    "comment" => [$uncomment($component), $comment($component), $html],
                    "uncomment" => [$comment($component), $uncomment($component), $html]
                ][$action]
            );
        }

        return $html;
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

        return $bodyContent;
    }
}
