<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use DOMDocument;
use DOMNode;

class ComponentExecutor extends ComponentManager
{
    /**
     * Executing component
     * 
     * @param string $component
     * @param array $attributes    
     * @param DOMNode $tag
     * @return void
     */
    public function execute_component(string $folder, string $component, object $attributes, DOMNode $tag): void
    {
        if (self::check("file", $folder, $component)) {
            if (!self::check("function_normal", $folder, $component) || !self::check("method_normal", $folder, $component)) {
                include_once self::get_file($folder, $component);
            }
        }

        $source = match (true) {
            self::check("method", $folder, $component) => (string) (new $folder)->$component($attributes),
            self::check("method_normal", $folder, $component) => (string) (function () use ($component, $attributes) {
                    @[$class, $method] = explode("::", $component);
                    return (string) (new $class)->$method($attributes); // Ejecuta el método y convierte el resultado a string
                })(),
            self::check("function", $folder, $component) => (string) self::valid_name_function($folder, $component)($attributes),
            self::check("function_normal", $folder, $component) => (string) $component($attributes),
            default => null
        };

        if ($source) {

            if (self::contains_html_base($source)) {
                $this->contains_html_base = true;
                $this->dom->loadHTML($source, LIBXML_NOERROR);
            } else {
                self::replace_component($source, $tag);
            }
        }
    }

    private function replace_component(string $source, DOMNode $tag): void
    {
        $dom = new DOMDocument(
            $this->dom_version,
            $this->dom_encoding
        );

        $previousErrorSetting = libxml_use_internal_errors(true);

        if ($dom->loadHTML(self::html_base($source, $this->dom_encoding), LIBXML_NOERROR)) {
            $body = $dom->getElementsByTagName("body")->item(0);

            $importedNodes = [];

            foreach ($body->childNodes as $child) {
                $importedNodes[] = $tag->ownerDocument->importNode($child->cloneNode(true), true);
            }

            foreach ($importedNodes as $importedNode) {
                $tag->parentNode->insertBefore($importedNode, $tag);
            }

            $tag->parentNode->removeChild($tag);
        }

        libxml_use_internal_errors($previousErrorSetting);
    }
}
