<?php

# Example with bootstrap 5.3.3: https://getbootstrap.com/

use Oscurlo\ComponentRenderer\ComponentRenderer;

include "../vendor/autoload.php";

$render = new ComponentRenderer(
    [
        # Layouts
        __DIR__ . "/Layouts" => ["Layout::system"],
        # Components
        __DIR__ . "/components" => ["Bootstrap::accordion", "Bootstrap::card"]
    ]
);

$i = 0;
$n = fn(int &$i) => ++$i;

$items = $render::json_encode(
    [
        [
            "title" => "Accordion Item #{$n($i)}",
            "body" => <<<HTML
            <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse
            plugin adds the appropriate classes that we use to style each element. These classes control the overall
            appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
            custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
            within the <code>.accordion-body</code>, though the transition does limit overflow.
            HTML
        ],
        [
            "title" => "Accordion Item #{$n($i)}",
            "body" => <<<HTML
            <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse
            plugin adds the appropriate classes that we use to style each element. These classes control the overall
            appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
            custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
            within the <code>.accordion-body</code>, though the transition does limit overflow.
            HTML
        ]
    ]
);

$now = "<time>" . date("l jS \of F Y h:i:s A") . "</time>";

$render->start()->print(<<<HTML
<Layout::system>
    <div class="container mt-5">
        <Bootstrap::card footer="{$now}">
            <Bootstrap::accordion id="test">
                {$items}
            </Bootstrap::accordion>
        </Bootstrap::card>
    </div>
</Layout::system>
HTML)->end();
