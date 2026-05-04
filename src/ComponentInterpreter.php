<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use DOMDocument;
use Oscurlo\ComponentRenderer\Support\HtmlHelper;

class ComponentInterpreter extends ComponentExecutor
{
    /**
     * Parse the given HTML, find registered component tags, execute them,
     * and return the final rendered HTML.
     *
     * @param  string $html
     * @return string
     */
    protected function interpreter(string $html): string
    {
        if (!$this->component_manager) {
            return $html;
        }

        $this->dom = new DOMDocument($this->dom_version, $this->dom_encoding);
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = true;

        $previousErrorSetting = libxml_use_internal_errors(true);

        $this->contains_html_base = HtmlHelper::isDocument($html);

        $this->convert_to_valid_tag($html);

        $loaded = $this->contains_html_base
            ? $html
            : HtmlHelper::wrap($html, $this->dom_encoding);

        if ($this->dom->loadHTML($loaded, LIBXML_NOERROR)) {
            // Loop until no more components are rendered — handles nested components
            do {
                $rendered = false;

                foreach ($this->component_manager as $references => $values) {
                    foreach ($values as $component) {
                        $rendered =
                            $this->processComponent($references, $component) ||
                            $rendered;
                    }
                }
            } while ($rendered);
        }

        libxml_use_internal_errors($previousErrorSetting);

        $output = $this->dom->saveHTML();

        return HtmlHelper::tidy(
            $this->contains_html_base
                ? $output
                : HtmlHelper::unwrap(
                    $output,
                    $this->dom_version,
                    $this->dom_encoding,
                ),
        );
    }

    /**
     * Find and execute all instances of a single component in the current DOM.
     *
     * @param  string $folder
     * @param  string $component
     * @return bool   Whether at least one instance was rendered
     */
    private function processComponent(string $folder, string $component): bool
    {
        $rendered = false;

        // Reverse order so replacing a node doesn't invalidate sibling indices
        foreach (
            array_reverse($this->getTagsForComponent($folder, $component))
            as $tag
        ) {
            $rendered =
                $this->execute_component(
                    $folder,
                    $component,
                    $this->get_params($tag),
                    $tag,
                ) || $rendered;
        }

        return $rendered;
    }

    /**
     * Collect all DOM nodes matching a component's valid tag name.
     * Snapshot into array first so DOM mutations don't break iteration.
     *
     * @param  string $folder
     * @param  string $component
     * @return array<int, \DOMNode>
     */
    private function getTagsForComponent(
        string $folder,
        string $component,
    ): array {
        $tags = $this->dom->getElementsByTagName(
            $this->valid_tag($folder, $component),
        );

        $snapshot = [];
        foreach ($tags as $tag) {
            $snapshot[] = $tag;
        }

        return $snapshot;
    }
}
