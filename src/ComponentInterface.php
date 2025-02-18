<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

class ComponentInterface
{
    /**
     * @param array  $values
     * @param object $props
     */
    public static function interface(array $values, object &$props): void
    {
        foreach ($values as $key => $type) {
            if (property_exists($props, $key)) {
                $value = $props->{$key};

                $props->{$key} = match (strtolower($type)) {
                    "boolean", "bool" => self::boolean($value),
                    "integer", "int" => intval($value),
                    "double" => doubleval($value),
                    "float" => floatval($value),
                    "string", "str" => (string) $value,
                    "array" => (array) json_decode($value, true),
                    "object" => json_decode($value, true),
                    default => $value
                };
            }
        }
    }

    /**
     * @param
     * @return bool
     */
    private static function boolean($val): bool
    {
        return filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === true;
    }
}
