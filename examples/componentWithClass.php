<?php

use Oscurlo\ComponentRenderer\ComponentRenderer;


include "../vendor/autoload.php";

$ComponentRenderer = new ComponentRenderer(
    source: "./components/"
);

# Example with bootstrap 5.3.3: https://getbootstrap.com/

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>component with class</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>

<body class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <?php $ComponentRenderer->start(["Bootstrap::card", "Bootstrap::accordion"]) ?>
    <div class="container">
        <div class="row">
            <div class="col-6">
                <Bootstrap::card title="hola wenas">
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Ipsa neque atque labore optio laborum
                    accusantium,
                    expedita obcaecati libero. Sint voluptatem nulla cupiditate, quam in, provident exercitationem quas
                    cumque
                    earum eaque error necessitatibus!
                    <b>hola</b>
                </Bootstrap::card>
            </div>
            <div class="col-6">
                <Bootstrap::accordion id="accordionExample" index-collapse="0">
                    <?= $ComponentRenderer::json_encode(
                        [
                            [
                                "title" => "Accordion Item #1",
                                "body" => <<<HTML
                                <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse
                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                HTML
                            ],
                            [
                                "title" => "Accordion Item #2",
                                "body" => <<<HTML
                                <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse
                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                HTML
                            ]
                        ]
                    ) ?>
                </Bootstrap::accordion>
            </div>
        </div>
    </div>
    <?php $ComponentRenderer->end() ?>
</body>

</html>