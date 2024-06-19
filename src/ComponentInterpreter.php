<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use DOMDocument;
use DOMNode;

class ComponentInterpreter extends ComponentExecutor
{
    protected function interpreter(string $html, array $components): string
    {
        $dom = new DOMDocument(
            version: $this->dom_version,
            encoding: $this->dom_encoding
        );
        libxml_use_internal_errors(true);

        $componentsProcessed = true;
        while ($componentsProcessed) {
            $componentsProcessed = false;
            foreach ($components as $component) if (self::component_exists($component)) {
                ["component" => $comp, "method" => $meth] = self::split_component($component);
                $function = $meth ? "{$comp}::{$meth}" : $comp;

                $html = str_ireplace(["<{$function}", "{$function}>"], ["<object", "object>"], self::uncomment_component($html, [$component]));

                if ($dom->loadHTML("<html><body>{$html}</body></html>", LIBXML_NOERROR)) {
                    libxml_clear_errors();

                    $tags = $dom->getElementsByTagName("object");

                    if ($tags->length > 0) $componentsProcessed = true;

                    $tagsList = [];
                    foreach ($tags as $tag) $tagsList[] = $tag;

                    foreach ($tagsList as $tag) {
                        self::execute_component($component, self::get_params($tag, $dom), $tag);
                        $html = $dom->saveHTML();
                    }
                }

                $html = str_ireplace(["<object", "object>"], ["<{$function}", "{$function}>"], self::comment_component($html, [$component]));
            }
        }

        return $html;
    }

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
