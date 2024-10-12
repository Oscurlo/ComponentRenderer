<?php

# Example with bootstrap 5.3.3: https://getbootstrap.com/

use Oscurlo\ComponentRenderer\ComponentRenderer;

include "../vendor/autoload.php";

$render = new ComponentRenderer(
    [
        # Layouts
        __DIR__ . "/Layouts" => "Layout::center",
        # Components
        __DIR__ . "/Components" => ["Bootstrap::card", "Image"]
    ]
);

?>

<?php $render->start() ?>
<Layout::center title="Explicit Width And Height">
    <div class="container">
        <Bootstrap::card title="Image">
            <div class="row">
                <div class="col-6">
                    <Image src="img/logo.png" alt="Image with explicit width and height" class="img-fluid" />
                </div>
                <div class="col-6">
                    <Image src="img/logo.png" alt="Image with explicit width and height" class="img-fluid" />
                </div>
            </div>
        </Bootstrap::card>
    </div>
</Layout::center>
<?php $render->end() ?>