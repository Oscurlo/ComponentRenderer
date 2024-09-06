<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use DOMDocument;
use DOMNode;
use Exception;

class ComponentExecutor extends ComponentManager
{
    /**
     * Executing component
     * 
     * @param string $component
     * @param array $attributes    
     * @param DOMNode $tag
     * 
     * @throws Exception Component not found
     */
    public function execute_component(string $folder, string $component, object $attributes, DOMNode $tag, DOMDocument $domMain): void
    {

        $dom = new DOMDocument(
            version: $this->dom_version,
            encoding: $this->dom_encoding
        );

        libxml_use_internal_errors(true);

        $is_a = match (true) {
            strpos($component, "::") !== false => "method_static",
                // strpos($component, "->") !== false => "method_public",
            default => "function",
        };

        @[$classname, $method] = explode(strpos($component, "::") !== false ? "::" : "->", $component);

        if (!function_exists($classname) || !class_exists($classname)) include_once self::get_file($folder, $component);

        if (function_exists($classname) || class_exists($classname) && method_exists($classname, (string)$method)) {

            $source = match ($is_a) {
                "method_static" => $classname::$method($attributes),
                // "method_public" => (new $classname)->$method($attributes),
                "function" => $classname($attributes),
            };

            // if (empty($source)) new Exception("No valid empty value");

            $contains_html_base = self::contains_html_base($source);

            if ($contains_html_base) {
                $this->contains_html = true;
                $domMain->loadHTML($source, LIBXML_NOERROR);
                return;
            }

            if (!$dom->loadHTML(self::html_base($source, $this->dom_encoding), LIBXML_NOERROR)) throw new Exception("Error Processing Request");

            $body = $dom->getElementsByTagName("body")->item(0);

            if (!$body) throw new Exception("No body tag found in the component's HTML");

            $importedNodes = [];
            foreach ($body->childNodes as $child) $importedNodes[] = $tag->ownerDocument->importNode($child->cloneNode(true), true);

            foreach ($importedNodes as $importedNode) $tag->parentNode->insertBefore($importedNode, $tag);

            $tag->parentNode->removeChild($tag);
        } else {
            throw new Exception("An error occurred while executing the function: {$component}");
        }
    }
}
