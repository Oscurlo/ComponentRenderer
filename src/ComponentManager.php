<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use Exception;

class ComponentManager
{
    public string $dom_version = "1.0";
    public string $dom_encoding = "UTF-8";
    protected ?string $component_path = null;
    protected bool $component_path_is_defined = false;

    /**
     * Set Path
     * 
     * @param string $source
     * @throws Exception
     * @return void
     */
    public function set_component_path(string $source): void
    {
        if (!is_dir($source)) throw new Exception("We couldn't find the folder");

        $this->component_path_is_defined = true;
        $this->component_path = rtrim($source, "/\\");
    }

    /**
     * Get Path
     * 
     * @return string
     */
    public function get_component_path(): ?string
    {
        return $this->component_path;
    }

    /**
     * Check Path
     * 
     * @param string $component
     * @return bool
     */
    protected function component_exists(string $component): bool
    {
        return file_exists($this->get_component_file($component));
    }

    /**
     * Get Component Info
     * 
     * @param string $component
     * @return string
     */
    protected function get_component_file(string $component): string
    {
        ["folder" => $folder, "component" => $file] = self::split_component($component);
        $path = $this->get_component_path();

        return $folder ? "{$path}/{$folder}/{$file}.php" : "{$path}/{$file}.php";
    }

    /**
     * Get Component Info
     * 
     * @param string $component
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
     * json_encode
     * 
     * @param array $value
     * @return bool|string
     */
    static function json_encode(array $value): bool|string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    protected function comment_component(string $html, array $components): string
    {
        return self::comment_or_uncomment("comment", $html, $components);
    }
    protected function uncomment_component(string $html, array $components): string
    {
        return self::comment_or_uncomment("uncomment", $html, $components);
    }

    private function comment_or_uncomment($action, $html, $components)
    {
        $comment = fn ($tag) => ["<!-- <{$tag}", "</{$tag}> -->"];
        $uncomment = fn ($tag) => ["<{$tag}", "</{$tag}>"];

        foreach ($components as $component) {
            $html = str_ireplace(
                ...[
                    "comment" => [$uncomment($component), $comment($component), $html],
                    "uncomment" => [$comment($component), $uncomment($component), $html]
                ][$action]
            );
        }

        return $html;
    }

    static function extract_attributes($main, $columns, $encoding = "UTF-8"): string
    {
        $attrs = [];
        foreach ($main as $key => $value) {
            if (in_array($key, $columns)) {
                $attrs[] = "{$key}=\"" . htmlspecialchars($value, ENT_QUOTES, $encoding) . "\"";
            }
        }
        return implode(" ", $attrs);
    }

    static function html_base($html, $encoding = "UTF-8"): string
    {
        return <<<HTML
        <html>
            <meta charset="{$encoding}">
            <body>{$html}</body>
        </html>
        HTML;
    }
}
