<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use DOMDocument;
use DOMNode;

class ComponentInterpreter extends ComponentExecutor
{
    /**
     * This method is responsible for searching for components within the html
     * 
     * @param string $html
     * @param array $components
     */
    protected function interpreter(string $html, array $components): string
    {
        $dom = new DOMDocument(
            version: $this->dom_version,
            encoding: $this->dom_encoding
        );
        libxml_use_internal_errors(true);

        # A loop is executed that is responsible for searching for the components until they are completely processed in the html
        $componentsProcessed = true;

        while ($componentsProcessed) {
            $componentsProcessed = false;
            # I loop to find each component.
            foreach ($components as $component) if (self::component_exists($component)) {
                ["component" => $comp, "method" => $meth] = self::split_component($component);
                $function = $meth ? "{$comp}::{$meth}" : $comp;

                /**
                 * The DOMDocument class is very strict and I can't get the tags as I receive them,
                 * this gave me trouble for a while until I came up with this solution.
                 * I simply rename the "Component -> Object" tag to a valid html element.
                 */
                $html = str_replace(["<{$function}", "{$function}>"], ["<object", "object>"], self::uncomment_component($html, [$component]));

                if ($dom->loadHTML(self::html_base($html, $this->dom_encoding), LIBXML_NOERROR)) {
                    libxml_clear_errors();

                    $tags = $dom->getElementsByTagName("object");

                    if ($tags->length > 0) $componentsProcessed = true;

                    $tagsList = [];
                    foreach ($tags as $tag) $tagsList[] = $tag;

                    foreach ($tagsList as $tag) {
                        self::execute_component($component, self::get_params($tag, $dom), $tag);
                        $html = self::get_body(
                            $dom->saveHTML(),
                            $this->dom_version,
                            $this->dom_encoding
                        );
                    }
                }

                $html = str_replace(["<object", "object>"], ["<{$function}", "{$function}>"], self::comment_component($html, [$component]));
            }
        }

        return $html;
    }

    /**
     * Gets the attributes of the component
     * 
     * @param DOMNode $tag
     * @param DOMDocument $doms
     */
    protected function get_params(DOMNode $tag, DOMDocument $dom): array
    {
        $attrs = [];
        $attrs["children"] = "";

        foreach ($tag->attributes as $attr) $attrs[$attr->nodeName] = $attr->nodeValue;

        if (!empty($tag->childNodes->count())) {
            $attrs["children"] = "";

            foreach ($tag->childNodes as $child) $attrs["children"] .= $dom->saveHTML($dom->importNode($child, true));
        }

        $attrs["textContent"] = $tag->textContent;

        return $attrs;
    }
}
