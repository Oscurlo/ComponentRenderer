<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use Exception;

class ComponentManager
{
    protected string $dom_version = "1.0";
    protected string $dom_encoding = "UTF-8";
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
        if (!is_dir($source)) {
            throw new Exception("");
        }

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

    /**
     * chatgpt
     * 
     * https://chatgpt.com/share/e842aa8c-cb9c-43bf-bcee-b2667ebc3d49
     * 
     */

    static function json_cleaned(string $json): string
    {
        $json = str_replace(['\\/', '\\r\\n', '\\'], ['/', ' ', ''], $json);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error decodificando JSON: ' . json_last_error_msg());
        }

        return $json;
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
        foreach ($components as $component) {
            $html = str_ireplace(
                ...[
                    "comment" => [["<{$component}", "{$component}>"], ["<!-- <{$component}", "{$component}> -->"], $html],
                    "uncomment" => [["<!-- <{$component}", "{$component}> -->"], ["<{$component}", "{$component}>"], $html]
                ][$action]
            );
        }

        return $html;
    }
}
