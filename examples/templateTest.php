<?php

# Example with bootstrap 5.3.3: https://getbootstrap.com/

use Oscurlo\ComponentRenderer\ComponentRenderer;
use Oscurlo\ComponentRenderer\Examples\Layouts\TestLayout;

include "../vendor/autoload.php";

$render = new ComponentRenderer([
    TestLayout::class => "system"
]);

$render->render(<<<XML
<TestLayout::system>
    hola wenas
</TestLayout::system>
XML);