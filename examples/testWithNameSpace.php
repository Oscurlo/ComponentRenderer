<?php

# Example with bootstrap 5.3.3: https://getbootstrap.com/

use Oscurlo\ComponentRenderer\ComponentRenderer;
use Oscurlo\ComponentRenderer\Examples\Components\Test1;

include "../vendor/autoload.php";

$render = new ComponentRenderer([
    __DIR__ . "/Layouts" => "Layout::center",
    __DIR__ . "/components" => "Bootstrap::card",
    Test1::class => "select"
]);

$title = "Test with namespace";

$render->render(<<<XML
<Layout::center title="{$title}">
    <Bootstrap::card>
        <Test1::select class="form-control">
            <option value="{id}">{name}: {desc}</option>
        </Test1::select>
    </Bootstrap::card>
</Layout::center>
XML);
