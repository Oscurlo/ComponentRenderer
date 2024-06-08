<?php

use Oscurlo\ComponentRenderer\ComponentRenderer;

require_once "../vendor/autoload.php";

$renderer = new ComponentRenderer(__DIR__ . "/components"); // Ajusta el path a donde tienes los componentes

$components = ["GoodbyeWorld::sayGoodbye", "HelloWorld"];

$renderer->start($components);

echo <<<HTML
<GoodbyeWorld::sayGoodbye name="Bob">This should be replaced</GoodbyeWorld::sayGoodbye>
<HelloWorld name="Esteban">This should be replaced</HelloWorld>
HTML;

$renderer->end();
