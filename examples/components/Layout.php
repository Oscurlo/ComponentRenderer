<?php

declare(strict_types=1);

use Oscurlo\ComponentRenderer\Component;

function Layout(object $props): string
{
    $props->title ??= "...";

    return Component::render(
        Component::template(
            filename: dirname(__DIR__) . "/layout/Layout.blade.php",
            props: $props
        )
    );
}
