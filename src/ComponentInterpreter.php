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
    protected function interpreter(string $html): string
    {
        if (!$this->component_folders) {
            return $html;
        }

        $this->dom = new DOMDocument(
            $this->dom_version,
            $this->dom_encoding
        );

        $previousErrorSetting = libxml_use_internal_errors(true);

        $this->contains_html_base = self::contains_html_base($html);

        self::convert_to_valid_tag($html);

        if ($this->dom->loadHTML($this->contains_html_base ? $html : self::html_base($html), LIBXML_NOERROR)) {
            foreach ($this->component_folders as $folders) {
                self::processFolders($folders);
            }
        }

        libxml_use_internal_errors($previousErrorSetting);

        return $this->contains_html_base ? $this->dom->saveHTML() : self::get_body($this->dom->saveHTML());
    }

    private function processFolders(array $folders)
    {
        foreach ($folders as $folder => $components) {
            foreach ($components as $component) {
                self::processComponent($folder, $component);
            }
        }
    }

    /**
     * @param string $folder
     * @param string $component
     */
    private function processComponent($folder, $component)
    {
        if (self::component_exists($folder, $component)) {
            foreach (self::getTagsForComponent($folder, $component) as $tag) {
                self::execute_component($folder, $component, self::get_params($tag), $tag);
            }
        }
    }

    /**
     * @param string $component
     * @return array
     */
    private function getTagsForComponent($folder, $component)
    {
        $tagsList = [];


        $tags = $this->dom->getElementsByTagName(
            self::valid_tag(
                $folder,
                $component
            )
        );

        foreach ($tags as $tag) {
            $tagsList[] = $tag;
        }

        return $tagsList;
    }
}
