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
    public function interpreter(string $html): string
    {

        $dom = new DOMDocument(
            version: $this->dom_version,
            encoding: $this->dom_encoding
        );

        libxml_use_internal_errors(true);

        $html = self::comment_all_components($html);

        foreach ($this->component_folders as $folders) {
            foreach ($folders as $folder => $components) {
                foreach ($components as $component) {

                    if (self::component_exists($folder, $component)) {
                        $html = self::replace_tag($component, $this->tag, self::uncomment_component($html, [$component]));

                        if ($dom->loadHTML(self::html_base($html), LIBXML_NOERROR)) {
                            libxml_clear_errors();

                            $tags = $dom->getElementsByTagName($this->tag);

                            $tagsList = [];
                            foreach ($tags as $tag) $tagsList[] = $tag;

                            foreach ($tagsList as $tag) {
                                $attributes = self::get_params($tag, $dom);
                                self::execute_component($folder, $component, $attributes, $tag);

                                $html = self::get_body(
                                    $dom->saveHTML(),
                                    $this->dom_version,
                                    $this->dom_encoding
                                );
                            }
                        }

                        $html = self::replace_tag($this->tag, $component, self::comment_component($html, [$component]));
                    }
                }
            }
        }

        return $html;
    }
}
