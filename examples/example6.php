<?php

/**
 * Example 6 — Nested components + get_attributes()
 *
 * Shows how components can render other components internally,
 * and how get_attributes() forwards HTML attributes cleanly
 * without leaking internal props like "children" or custom ones.
 *
 * Also demonstrates Bootstrap::accordion, the most complex built-in component,
 * which receives a JSON-encoded array as its text content.
 */

declare(strict_types=1);

use Oscurlo\ComponentRenderer\Component;
use Oscurlo\ComponentRenderer\Examples\Components\Bootstrap;

include_once "../vendor/autoload.php";

$faq = json_encode([
    [
        "title" => "What is ComponentRenderer?",
        "body"  => "A lightweight PHP library that lets you write reusable HTML components — similar to React, but server-side and without a build step.",
    ],
    [
        "title" => "Does it require a template engine?",
        "body"  => "No. It works with plain PHP and standard HTML. The <code>{{ }}</code> syntax in templates is optional and compiled on the fly.",
    ],
    [
        "title" => "Which PHP versions are supported?",
        "body"  => "PHP 8.0 and above. Tested on 8.0, 8.1, 8.2, 8.3, and 8.5.",
    ],
    [
        "title" => "Can components call other components?",
        "body"  => "Yes — components can nest freely. The interpreter resolves them recursively until the DOM is fully expanded.",
    ],
]);

echo Component::render(
    html: <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Example 6 — Nested components + accordion</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="p-4">
        <h1 class="mb-4">Nested components</h1>

        <Bootstrap::card card-title="FAQ — accordion inside a card">
            <Bootstrap::accordion id="faq-accordion" index-collapse="0">
                {$faq}
            </Bootstrap::accordion>
        </Bootstrap::card>

    </body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </html>
    HTML,
    components: [
        Bootstrap::class => ["card", "accordion"],
    ],
);
