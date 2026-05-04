<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use DOMDocument;
use DOMNode;
use Oscurlo\ComponentRenderer\Concerns\HandlesDom;
use Oscurlo\ComponentRenderer\Support\HtmlHelper;

class ComponentExecutor extends ComponentRegistry
{
    use HandlesDom;

    private array $included_files = [];

    /**
     * Execute a component and replace its DOM node with the rendered output.
     *
     * @param  string  $references  Namespace, class, or directory path
     * @param  string  $component   Component name
     * @param  object  $attributes  Props extracted from the tag
     * @param  DOMNode $tag         The DOM node to replace
     * @return bool                 Whether the component was rendered
     */
    public function execute_component(
        string $references,
        string $component,
        object $attributes,
        DOMNode $tag
    ): bool {
        $source = is_dir($references)
            ? $this->executeFromDirectory($references, $component, $attributes)
            : $this->executeFromReference($references, $component, $attributes);

        if (!$source) {
            return false;
        }

        if (HtmlHelper::isDocument($source)) {
            $this->contains_html_base = true;
            $this->dom->loadHTML($source, LIBXML_NOERROR);
            return true;
        }

        return $this->replace_component($source, $tag);
    }

    /**
     * Execute a component defined as a namespaced method or function.
     *
     * @param  string $references
     * @param  string $component
     * @param  object $attributes
     * @return string|null
     */
    private function executeFromReference(
        string $references,
        string $component,
        object $attributes
    ): ?string {
        return match (true) {
            $this->check("method", $references, $component)
                => (string) (new $references())->$component($attributes),
            $this->check("function", $references, $component)
                => (string) $this->valid_name_function($references, $component)($attributes),
            default => null,
        };
    }

    /**
     * Execute a component defined as a file inside a directory.
     *
     * @param  string $references
     * @param  string $component
     * @param  object $attributes
     * @return string|null
     */
    private function executeFromDirectory(
        string $references,
        string $component,
        object $attributes
    ): ?string {
        $key = "{$references}->{$component}";

        if (!isset($this->included_files[$key])) {
            include_once $this->get_file($references, $component);
            $this->included_files[$key] = true;
        }

        $split  = explode("::", $component);
        $class  = $split[0] ?? "";
        $method = $split[1] ?? "";

        return match (true) {
            $this->check("method_normal", $references, $component)
                => (string) (new $class())->$method($attributes),
            $this->check("function_normal", $references, $component)
                => (string) $component($attributes),
            default => null,
        };
    }

    /**
     * Replace a DOM node with the HTML produced by the component.
     *
     * @param  string  $source
     * @param  DOMNode $tag
     * @return bool
     */
    private function replace_component(string $source, DOMNode $tag): bool
    {
        $dom = new DOMDocument($this->dom_version, $this->dom_encoding);

        $previousErrorSetting = libxml_use_internal_errors(true);
        $replaced = false;

        if ($dom->loadHTML(HtmlHelper::wrap($source, $this->dom_encoding), LIBXML_NOERROR)) {
            $body = $dom->getElementsByTagName("body")->item(0);

            $importedNodes = [];
            foreach ($body->childNodes as $child) {
                $importedNodes[] = $tag->ownerDocument->importNode(
                    $child->cloneNode(true),
                    true
                );
            }

            foreach ($importedNodes as $importedNode) {
                $tag->parentNode->insertBefore($importedNode, $tag);
            }

            $tag->parentNode->removeChild($tag);
            $replaced = true;
        }

        libxml_use_internal_errors($previousErrorSetting);

        return $replaced;
    }
}
