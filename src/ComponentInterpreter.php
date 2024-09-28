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
        if (empty($this->component_folders)) return $html;

        if (!$this->dom instanceof DOMDocument) $this->dom = new DOMDocument($this->dom_version, $this->dom_encoding);

        $previousErrorSetting = libxml_use_internal_errors(true);

        $contains_html_base = self::contains_html_base($html);

        if ($contains_html_base) $this->contains_html = true;

        $html = self::convert_to_valid_tag($html);
        $domLoaded = $this->dom->loadHTML($contains_html_base ? $html : self::html_base($html, $this->dom_encoding), LIBXML_NOERROR);

        if ($domLoaded) foreach ($this->component_folders as $folders) self::processFolders($folders);

        libxml_use_internal_errors($previousErrorSetting);

        return $this->contains_html ? $this->dom->saveHTML() : self::get_body($this->dom->saveHTML());
    }

    private function processFolders(array $folders): void
    {
        foreach ($folders as $folder => $components)
            if (is_string($folder) && is_array($components))
                foreach ($components as $component)
                    if (is_string($component) || is_array($component))
                        self::processComponent($folder, $component);
    }

    /**
     * @param string $folder
     * @param string $component
     * @return array
     */
    private function processComponent(string $folder, string $component): void
    {
        if (self::component_exists($folder, $component))
            foreach ($this->getTagsForComponent($component) as $tag)
                self::execute_component($folder, $component, self::get_params($tag), $tag);
    }

    /**
     * @param string $component
     * @return array
     */
    private function getTagsForComponent(string $component): array
    {
        $tagsList = [];
        $tags = $this->dom->getElementsByTagName(self::valid_tag($component));

        foreach ($tags as $tag) $tagsList[] = $tag;

        return $tagsList;
    }
}
