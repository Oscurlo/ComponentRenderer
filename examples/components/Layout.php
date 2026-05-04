<?php

declare(strict_types=1);

use Oscurlo\ComponentRenderer\Component;

function Layout(object $props): string
{
    $props->title ??= "Example";

    return Component::render(
        Component::template(
            filename: __DIR__ . "/../layout/Layout.php",
            props: $props,
        ),
    );
}
