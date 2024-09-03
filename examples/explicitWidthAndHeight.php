<?php

use Oscurlo\ComponentRenderer\ComponentRenderer;

include "../vendor/autoload.php";

$ComponentRenderer = new ComponentRenderer(
    [
        __DIR__ . "/components" => "Image"
    ]
);

# Example with bootstrap 5.3.3: https://getbootstrap.com/

$pre = fn(mixed $value) => "<pre>" . json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "</pre>";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>component with class</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>

<body class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <?php $ComponentRenderer->start() ?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <Image src="./img/logo.png" alt="Image with explicit width and height" class="img-fluid" />
            </div>
        </div>
    </div>
    <?php $ComponentRenderer->end() ?>
</body>

</html>