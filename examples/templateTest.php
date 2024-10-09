<?php

use Examples\Layouts\TestLayout;
use Oscurlo\ComponentRenderer\ComponentRenderer;

include "../vendor/autoload.php";

$render = new ComponentRenderer([
    TestLayout::class => "system"
]);

$render->render(<<<XML
<TestLayout::system>
    hola wenas
</TestLayout::system>
XML);