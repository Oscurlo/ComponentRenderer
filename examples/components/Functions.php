<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Examples\Components;

use Oscurlo\ComponentRenderer\Component;

function __generate_random_id(): string
{
    return strtoupper(
        implode(
            "_",
            str_split(bin2hex(random_bytes(12)), 4)
        )
    );
}

function __render_class(string $class): string
{
    return trim(
        implode(
            " ",
            array_unique(explode(" ", $class))
        )
    );
}

function Container(object $props): string
{
    $props->class ??= "";
    $props->grid ??= "";

    if (!empty($props->grid)) {
        $props->grid = "-{$props->grid}";
    }

    $props->class = __render_class("container{$props->grid} {$props->class}");

    $attrs = Component::get_attributes($props, ["grid"]);

    return <<<HTML
    <div {$attrs}>
        {$props->children}
    </div>
    HTML;
}

function Row(object $props): string
{
    $props->class ??= "";
    $props->class = __render_class("row {$props->class}");

    $attrs = Component::get_attributes($props);

    return <<<HTML
    <div {$attrs}>
        {$props->children}
    </div>
    HTML;
}

function Column(object $props): string
{
    $props->class ??= "";
    $props->size ??= "";

    $props->class = !empty($props->size) ? "col-{$props->size} {$props->class}" : "col {$props->class}";

    $props->class = __render_class($props->class);
    $attrs = Component::get_attributes($props, ["size"]);

    return <<<HTML
    <div {$attrs}>
        {$props->children}
    </div>
    HTML;
}

function InputField(object $props): string
{
    $props->id ??= __generate_random_id();
    $props->{"label-text"} ??= "...";

    $attrs = Component::get_attributes($props, ["label-text"]);

    return <<<HTML
    <label for="{$props->id}" class="form-label">{$props->{"label-text"} }</label>
    <input {$attrs}>
    HTML;
}

function TextareaField(object $props): string
{
    $props->id ??= __generate_random_id();
    $props->{"label-text"} ??= "...";

    $attrs = Component::get_attributes($props, ["label-text"]);

    $props->children = trim(
        str_replace(PHP_EOL, "", $props->children)
    );

    while (str_contains($props->children, "  ")) {
        $props->children = str_replace(
            ["  ", " "],
            " ",
            $props->children
        );
    }

    return <<<HTML
    <label for="{$props->id}" class="form-label">{$props->{"label-text"} }</label>
    <textarea {$attrs}>{$props->children}</textarea>
    HTML;
}
