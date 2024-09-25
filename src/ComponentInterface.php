<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

class ComponentInterface
{
    public static function interface(array $values, object &$props): void
    {
        foreach ($values as $key => $type) if (isset($props->{$key})) {
            $value = $props->{$key};

            $props->{$key} = match (strtolower(string: $type)) {
                "boolean" | "bool" => boolval(value: $value),
                "integer" | "int" => intval(value: $value),
                "double" => doubleval(value: $value),
                "float" => floatval(value: $value),
                "string" | "str" => (string)$value,
                "array" => (array)json_decode(json: $value, associative: true),
                "object" => json_decode(json: $value, associative: true),
                    // "resource" => false, # No se que es esto :c
                default => $value,
            };
        }
    }
}
