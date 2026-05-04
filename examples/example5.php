<?php

/**
 * Example 5 — PropsCaster: type-casting HTML attributes
 *
 * All HTML attributes arrive as strings from DOMDocument.
 * PropsCaster::interface() lets you declare the real PHP type for each prop,
 * so your component logic can work with proper booleans, ints, floats, and arrays.
 *
 * Supported types: bool/boolean, int/integer, float, double, string/str, array, object
 */

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Examples;

use Oscurlo\ComponentRenderer\Component;
use Oscurlo\ComponentRenderer\PropsCaster;

include_once "../vendor/autoload.php";

// ── Component definitions ──────────────────────────────────────────────────

/**
 * A progress bar that receives numeric and boolean props.
 * Without PropsCaster, $props->value would be the string "75", not int 75.
 */
function ProgressBar(object $props): string
{
    PropsCaster::interface(
        [
            "value" => "int",
            "max" => "int",
            "striped" => "bool",
            "animate" => "bool",
        ],
        $props,
    );

    $props->value ??= 0;
    $props->max ??= 100;
    $props->striped ??= false;
    $props->animate ??= false;
    $props->label ??= "{$props->value}%";

    $percent = min(100, round(($props->value / $props->max) * 100));

    $classes = "progress-bar";
    if ($props->striped) {
        $classes .= " progress-bar-striped";
    }
    if ($props->animate) {
        $classes .= " progress-bar-animated";
    }

    return <<<HTML
    <div class="progress mb-3" style="height: 24px;">
        <div class="{$classes} bg-success"
             role="progressbar"
             style="width: {$percent}%"
             aria-valuenow="{$props->value}"
             aria-valuemin="0"
             aria-valuemax="{$props->max}">
            {$props->label}
        </div>
    </div>
    HTML;
}

/**
 * A tag cloud that receives a JSON-encoded array of tags.
 * PropsCaster converts the JSON string into a real PHP array.
 */
function TagCloud(object $props): string
{
    PropsCaster::interface(
        [
            "tags" => "array",
        ],
        $props,
    );

    $props->tags ??= [];

    $badges = implode(
        " ",
        array_map(
            fn($tag) => "<span class=\"badge bg-primary me-1\">{$tag}</span>",
            $props->tags,
        ),
    );

    return <<<HTML
    <div class="mb-3">
        {$badges}
    </div>
    HTML;
}

// ── Render ─────────────────────────────────────────────────────────────────

echo Component::render(
    html: <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Example 5 — PropsCaster</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="p-4">
        <h1 class="mb-4">PropsCaster — type-safe props</h1>

        <h5>Progress bars (int + bool props)</h5>
        <ProgressBar value="30" max="100" />
        <ProgressBar value="65" max="100" striped="true" />
        <ProgressBar value="90" max="100" striped="true" animate="true" label="Almost there!" />

        <h5 class="mt-4">Tag cloud (array prop from JSON)</h5>
        <TagCloud tags='["PHP","composer","DOMDocument","HTML","components"]' />
    </body>
    </html>
    HTML
    ,
    components: [
        __NAMESPACE__ ?: "\\" => ["ProgressBar", "TagCloud"],
    ],
);
