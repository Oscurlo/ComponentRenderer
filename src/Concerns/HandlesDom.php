<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Concerns;

use DOMDocument;
use DOMNode;

/**
 * Provides DOM state and prop extraction helpers.
 * Used by ComponentExecutor and ComponentInterpreter.
 */
trait HandlesDom
{
    protected DOMDocument $dom;

    /** @var string DOMDocument version */
    public string $dom_version = "1.0";

    /** @var string DOMDocument encoding */
    public string $dom_encoding = "UTF-8";

    /**
     * Extract props and children from a DOMNode tag into a plain object.
     *
     * @param  DOMNode $tag
     * @return object
     */
    protected function get_params(DOMNode $tag): object
    {
        $attrs = ["children" => ""];

        foreach ($tag->attributes as $attr) {
            $attrs[$attr->nodeName] = $attr->nodeValue;
        }

        if ($tag->childNodes->count() > 0) {
            $attrs["children"] = "";

            foreach ($tag->childNodes as $child) {
                $attrs["children"] .= $this->dom->saveHTML(
                    $this->dom->importNode($child, true)
                );
            }
        }

        $attrs["textContent"] = $tag->textContent;

        return (object) $attrs;
    }
}
