<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Concerns;

use Oscurlo\ComponentRenderer\Support\HtmlHelper;

/**
 * Provides attribute extraction helpers for component authors.
 * Used by ComponentManager and exposed as public API.
 */
trait HandlesAttributes
{
    /**
     * Extract a specific subset of attributes from a props object/array
     * and return them as an HTML attribute string.
     *
     * @deprecated Use self::get_attributes() instead
     * @param  array|object $main     Props source
     * @param  array        $columns  Attribute names to include
     * @param  string       $encoding Encoding for htmlspecialchars
     * @return string
     */
    public static function extract_attributes(
        array|object $main,
        array $columns,
        string $encoding = "UTF-8"
    ): string {
        $attrs = [];

        foreach ((array) $main as $key => $value) {
            if (in_array($key, $columns)) {
                $attrs[] = "{$key}=\"" . htmlspecialchars((string) $value, ENT_QUOTES, $encoding) . "\"";
            }
        }

        return implode(" ", $attrs);
    }

    /**
     * Build an HTML attribute string from a props object/array,
     * automatically excluding internal keys and empty values.
     *
     * @param  array|object $main    Props source
     * @param  array        $exclude Additional keys to exclude beyond the defaults
     * @return string
     */
    public static function get_attributes(array|object $main, array $exclude = []): string
    {
        $main = (array) $main;

        return self::extract_attributes(
            $main,
            array_filter(
                array_keys(array_filter($main, fn ($value): bool => !empty($value))),
                fn ($key): bool => !in_array($key, ["children", "textContent", ...$exclude])
            )
        );
    }
}
