<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use DOMDocument;
use DOMNode;

class ComponentExecutor extends ComponentManager
{
    private array $included_files;

    /**
     * Executing component
     *
     * @param  string  $references
     * @param  string  $component
     * @param  object  $attributes
     * @param  DOMNode $tag
     * @return void
     */
    public function execute_component(string $references, string $component, object $attributes, DOMNode $tag): void
    {
        if (!is_dir($references)) {
            $source = match (true) {
                self::check("method", $references, $component) => (string) (new $references())->$component(
                    $attributes
                ),
                self::check("function", $references, $component) => (string) self::valid_name_function(
                    $references,
                    $component
                )($attributes),
                default => null
            };
        } else {
            $key = "{$references}->{$component}";

            if (!isset($this->included_files[$key])) {
                include_once self::get_file(
                    $references,
                    $component
                );

                $this->included_files[$key] = true;
            }

            $method_normal_exists = self::check("method_normal", $references, $component);
            $function_normal_exists = self::check("function_normal", $references, $component);

            $source = match (true) {
                $method_normal_exists => (function () use ($component, $attributes) {
                    $split = explode("::", $component);
                    $class = $split[0] ?? "";
                    $method = $split[1] ?? "";

                    return (string) (new $class())->$method(
                        $attributes
                    );
                })(),
                $function_normal_exists => $component(
                    $attributes
                ),
                default => null
            };
        }

        if ($source) {
            if (self::contains_html_base($source)) {
                $this->contains_html_base = true;
                $this->dom->loadHTML(
                    $source,
                    LIBXML_NOERROR
                );
            } else {
                self::replace_component(
                    $source,
                    $tag
                );
            }
        }
    }

    /**
     * @param string  $source
     * @param DOMNode $tag
     */
    private function replace_component(string $source, DOMNode $tag): void
    {
        $dom = new DOMDocument(
            $this->dom_version,
            $this->dom_encoding
        );

        $previousErrorSetting = libxml_use_internal_errors(true);

        if ($dom->loadHTML(self::html_base($source, $this->dom_encoding), LIBXML_NOERROR)) {
            $body = $dom->getElementsByTagName("body")->item(0);

            $importedNodes = [];

            foreach ($body->childNodes as $child) {
                $importedNodes[] = $tag->ownerDocument->importNode(
                    $child->cloneNode(true),
                    true
                );
            }

            foreach ($importedNodes as $importedNode) {
                $tag->parentNode->insertBefore(
                    $importedNode,
                    $tag
                );
            }

            $tag->parentNode->removeChild(
                $tag
            );
        }

        libxml_use_internal_errors(
            $previousErrorSetting
        );
    }
}
