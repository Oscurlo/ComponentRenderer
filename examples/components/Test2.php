<?php

declare(strict_types=1);

namespace Examples\Components;

use Oscurlo\ComponentRenderer\Component;

function Test2(object $props): string
{
    $props->children ??= "pichula";

    return Component::render(<<<HTML
    <p>
        {$props->children}
    </p>
    HTML);
}