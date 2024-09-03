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
    public function execute_component(string $folder, string $component, object $attributes, DOMNode $tag): void
    {
        $dom = new DOMDocument(
            version: $this->dom_version,
            encoding: $this->dom_encoding
        );

        libxml_use_internal_errors(true);

        @[$classname, $method] = explode("::", $component);

        if (!function_exists($classname) || !class_exists($classname)) include_once self::get_file($folder, $component);

        if (function_exists($classname) || class_exists($classname) && method_exists($classname, (string)$method)) {

            // $source = call_user_func($component, $attributes);
            $source = $method ? $classname::$method($attributes) : $classname($attributes);


            if (empty($source)) new Exception("No valid empty value");

            $contains_html = strpos($source, "<html") !== false;

            if (!$dom->loadHTML($contains_html ? $source : self::html_base($source), LIBXML_NOERROR))
                throw new Exception("Error Processing Request");

            $body = $dom->getElementsByTagName($contains_html ? "*" : "body")->item(0);

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
