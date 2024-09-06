<?php

# Example with bootstrap 5.3.3: https://getbootstrap.com/

use Oscurlo\ComponentRenderer\ComponentRenderer;

include "../vendor/autoload.php";

$render = new ComponentRenderer(
    [
        # Layouts
        __DIR__ . "/Layouts" => ["Layout::center"],
        # Components
        __DIR__ . "/Components" => ["Bootstrap::card", "Image"]
    ]
);

$render->start();
echo <<<HTML
<Layout::center title="Explicit Width And Height">
    <Bootstrap::card title="Image">
        <Image src="./img/logo.png" alt="Image with explicit width and height" class="img-fluid" />
        <Image src="./img/logo.png" alt="Image with explicit width and height" class="img-fluid" />
    </Bootstrap::card>
</Layout::center>
HTML;
$render->end();
