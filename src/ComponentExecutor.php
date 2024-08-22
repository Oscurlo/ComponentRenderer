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
    protected function execute_component(string $component, array $attributes, DOMNode $tag)
    {
        ["component" => $comp, "method" => $meth] = $this->split_component($component);
        $dom = new DOMDocument(
            version: $this->dom_version,
            encoding: $this->dom_encoding
        );

        $function = $meth ? "{$comp}::{$meth}" : $comp;

        if (($meth && !class_exists($comp)) || (!$meth && !function_exists($comp))) include $this->get_component_file($component);

        if ($meth && class_exists($comp) && method_exists($comp, $meth)) {
            $source = self::html_base($comp::$meth($attributes), $this->dom_encoding);
            if (!$dom->loadHTML($source, LIBXML_NOERROR)) throw new Exception("An error occurred while executing the class: {$function}");
        } else if (!$meth && function_exists($comp)) {
            $source = self::html_base($comp($attributes), $this->dom_encoding);

            if (!$dom->loadHTML($source, LIBXML_NOERROR)) throw new Exception("An error occurred while executing the function: {$function}");
        } else {
            throw new Exception("Component {$function} not found.");
        }

        // $node = $dom->getElementsByTagName("body")->item(0)->firstChild;
        $body = $dom->getElementsByTagName("body")->item(0);

        if (!$body) throw new Exception("No body tag found in the component's HTML");

        // $importedNode = $tag->ownerDocument->importNode($node->cloneNode(true), true);
        $importedNodes = [];
        foreach ($body->childNodes as $child) $importedNodes[] = $tag->ownerDocument->importNode($child->cloneNode(true), true);

        foreach ($importedNodes as $importedNode) $tag->parentNode->insertBefore($importedNode, $tag);

        $tag->parentNode->removeChild($tag);

        // if (!$importedNode) throw new Exception("Failed to import node");

        // $tag->parentNode->replaceChild($importedNode, $tag);
    }
}
