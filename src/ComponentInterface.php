<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

class ComponentInterface
{
    public static function interface(array $values, object &$props): void
    {
        foreach ($values as $key => $type)
            if (isset($props->{$key})) {
                $value = $props->{$key};

                $props->{$key} = match (strtolower($type)) {
                    "boolean" | "bool" => self::is_true($value),
                    "integer" | "int" => intval($value),
                    "double" => doubleval($value),
                    "float" => floatval($value),
                    "string" | "str" => (string) $value,
                    "array" => (array) json_decode($value, true),
                    "object" => json_decode($value, true),
                    default => $value,
                };
            }
    }

    # Code taken from: https://www.php.net/manual/es/function.boolval.php
    public static function is_true($val, $return_null = false)
    {
        $boolval = is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val;
        return $boolval === null && !$return_null ? false : $boolval;
    }
}
