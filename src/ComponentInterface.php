<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

class ComponentInterface
{
    public static function interface(array $values, object &$props): void
    {
        foreach ($values as $key => $type) if (isset($props->{$key})) {
            $value = $props->{$key};

            $props->{$key} = match (strtolower($type)) {
                "boolean" | "bool" => filter_var($value, FILTER_VALIDATE_BOOLEAN),
                "integer" | "int" => intval($value),
                "double" => doubleval($value),
                "float" => floatval($value),
                "string" | "str" => (string)$value,
                "array" => (array)json_decode($value,  true),
                "object" => json_decode($value,  true),
                default => $value,
            };
        }
    }
}
