<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use Oscurlo\ComponentRenderer\Concerns\HandlesAttributes;
use Oscurlo\ComponentRenderer\Concerns\HandlesDom;
use Oscurlo\ComponentRenderer\Concerns\RendersOutput;
use Oscurlo\ComponentRenderer\Support\HtmlHelper;

/**
 * Central facade that wires together all concerns.
 * Extends ComponentRegistry (registration + resolution) and pulls in
 * the three traits for DOM state, attribute helpers, and output.
 *
 * @deprecated html_base(), get_body(), contains_html_base() — use HtmlHelper directly.
 */
class ComponentManager extends ComponentRegistry
{
    use HandlesDom;
    use HandlesAttributes;
    use RendersOutput;

    // -------------------------------------------------------------------------
    // Backwards-compatibility proxies — will be removed in next major version
    // -------------------------------------------------------------------------

    /**
     * @deprecated Use HtmlHelper::wrap() instead
     */
    public static function html_base(string $html, string $encoding = "UTF-8"): string
    {
        return HtmlHelper::wrap($html, $encoding);
    }

    /**
     * @deprecated Use HtmlHelper::unwrap() instead
     */
    public static function get_body(string $html, string $version = "1.0", string $encoding = "UTF-8"): string
    {
        return HtmlHelper::unwrap($html, $version, $encoding);
    }

    /**
     * @deprecated Use HtmlHelper::isDocument() instead
     */
    protected static function contains_html_base(string $html): bool
    {
        return HtmlHelper::isDocument($html);
    }
}
