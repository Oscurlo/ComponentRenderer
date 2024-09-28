<?php

use Oscurlo\ComponentRenderer\ComponentRenderer;

include "../vendor/autoload.php";

$render = new ComponentRenderer(
    [
        # Components
        __DIR__ . "/components" => "HelloWorld"
    ]
);

$render->start();
echo <<<HTML
<title>component with function</title>
<HelloWorld></HelloWorld>
<HelloWorld>Hola <b>wenas</b></HelloWorld>
HTML;
$render->end();
