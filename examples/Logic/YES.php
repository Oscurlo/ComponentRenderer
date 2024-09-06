<?php

declare(strict_types=1);

function YES(object $props): string
{
    $props->validate ??= "noEmpty";

    return match ($props->validate) {
        "isString" => is_string($props->condition),
        "isBool" => is_bool($props->condition),
        "noEmpty" => !empty($props->condition),
        "logic" => eval("return {$props->condition};"),
        default => false
    } ? $props->children : "";
}
