<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use DOMDocument;

class ComponentInterpreter extends ComponentExecutor
{
    /**
     * This method is responsible for searching for components within the html
     * 
     * @param string $html
     * @return string
     */
    public function interpreter(string $html): string
    {

        $dom = new DOMDocument(
            version: $this->dom_version,
            encoding: $this->dom_encoding
        );

        libxml_use_internal_errors(true);

        $contains_html_base = self::contains_html_base($html);

        if ($contains_html_base) $this->contains_html = true;

        $html = self::convert_to_valid_tag($html);

        if ($dom->loadHTML($contains_html_base ? $html : self::html_base($html, $this->dom_encoding), LIBXML_NOERROR)) {
            foreach ($this->component_folders as $folders) {
                foreach ($folders as $folder => $components) {
                    foreach ($components as $component) {
                        if (self::component_exists($folder, $component)) {

                            $tagsList = [];

                            $tags = $dom->getElementsByTagName(self::valid_tag($component));

                            foreach ($tags as $tag) $tagsList[] = $tag;

                            foreach ($tagsList as $tag) self::execute_component($folder, $component, self::get_params($tag, $dom), $tag, $dom);
                        }
                    }
                }
            }
        }

        return $this->contains_html ? $dom->saveHTML() : self::get_body($dom->saveHTML());
    }
}
