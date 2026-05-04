<?php

/**
 * Example 4 — Static API: Component::render()
 *
 * The simplest way to use the library: no instance needed.
 * Pass your HTML and component map directly to Component::render().
 *
 * Best for: one-off renders, helper functions, small scripts.
 */

declare(strict_types=1);

use Oscurlo\ComponentRenderer\Component;
use Oscurlo\ComponentRenderer\Examples\Components\Bootstrap;

include_once "../vendor/autoload.php";

$html = Component::render(
    html: <<<HTML
    <Layout>
        <Bootstrap::card card-title="Static render">
            <p>This entire page was rendered with a single <code>Component::render()</code> call.</p>
            <p>No instance, no buffer — just pass the HTML and the component map.</p>
        </Bootstrap::card>
    </Layout>
    HTML
    ,
    components: [
        __DIR__ . "\\components" => "Layout",
        Bootstrap::class => "card",
    ],
);

echo $html;
