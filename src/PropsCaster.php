<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

/**
 * Casts HTML attribute strings (always strings from DOMDocument) into the
 * proper PHP types declared by the component author via ::interface().
 *
 * Usage inside a component:
 *
 *   function Button(object $props): string
 *   {
 *       PropsCaster::interface(['disabled' => 'bool', 'count' => 'int'], $props);
 *       // $props->disabled is now a real bool, $props->count a real int
 *   }
 */
class PropsCaster
{
    /**
     * Cast a set of props to the declared types, in place.
     *
     * Supported types: bool/boolean, int/integer, float, double, string/str, array, object.
     * Unknown types leave the value untouched.
     *
     * @param  array<string, string> $values  Map of prop name → type name
     * @param  object                $props   Props object to mutate
     * @return void
     */
    public static function interface(array $values, object &$props): void
    {
        foreach ($values as $key => $type) {
            if (!property_exists($props, $key)) {
                continue;
            }

            $value = $props->{$key};

            $props->{$key} = match (strtolower($type)) {
                "boolean", "bool"   => self::boolean($value),
                "integer", "int"    => intval($value),
                "double"            => doubleval($value),
                "float"             => floatval($value),
                "string", "str"     => (string) $value,
                "array"             => (array) json_decode($value, true),
                "object"            => json_decode($value, true),
                default             => $value,
            };
        }
    }

    /**
     * Cast a value to boolean using PHP's filter_var for reliable string parsing
     * ("true", "1", "yes", "on" → true; "false", "0", "no", "off" → false).
     *
     * @param  mixed $val
     * @return bool
     */
    private static function boolean(mixed $val): bool
    {
        return filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === true;
    }
}
