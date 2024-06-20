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

        $params = $attributes;

        if ($meth && class_exists($comp) && method_exists($comp, $meth)) {
            $source = "<html><meta charset=\"$this->dom_encoding\"><body>{$comp::$meth($params)}</body></html>";

            if (!$dom->loadHTML($source, LIBXML_NOERROR)) throw new Exception("An error occurred while executing the class: {$function}");
        } else if (!$meth && function_exists($comp)) {
            $source = "<html><meta charset=\"$this->dom_encoding\"><body>{$comp($params)}</body></html>";

            if (!$dom->loadHTML($source, LIBXML_NOERROR)) throw new Exception("An error occurred while executing the function: {$function}");
        } else {
            throw new Exception("Component {$function} not found.");
        }

        $node = $dom->getElementsByTagName("body")->item(0)->firstChild;

        $importedNode = $tag->ownerDocument->importNode($node->cloneNode(true), true);

        if (!$importedNode) throw new Exception("Failed to import node");

        $tag->parentNode->replaceChild($importedNode, $tag);
    }
}
