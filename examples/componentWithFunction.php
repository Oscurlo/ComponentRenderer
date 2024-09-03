<?php

use Oscurlo\ComponentRenderer\ComponentRenderer;

include "../vendor/autoload.php";

$ComponentRenderer = new ComponentRenderer(
    [
        __DIR__ . "/components" => "HelloWorld"
    ]
);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>component with function</title>
</head>

<body>
    <?php $ComponentRenderer->start() ?>
    <HelloWorld></HelloWorld>
    <HelloWorld>Hola <b>wenas</b></HelloWorld>
    <?php $ComponentRenderer->end() ?>
</body>

</html>