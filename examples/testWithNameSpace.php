<?php

# Example with bootstrap 5.3.3: https://getbootstrap.com/

use Examples\Components\Test1;
use Oscurlo\ComponentRenderer\ComponentRenderer;
use function Examples\Components\Test2;

include "../vendor/autoload.php";

$render = new ComponentRenderer([
    # Layouts
    __DIR__ . "/Layouts" => "Layout::center",
        # Component with class
    Test1::class => "select"
]);

$title = "Test with namespace";

echo Test2(props: (object) []);

// $render->render(<<<XML
// <Test::select class="form-control">
//     <option value="{id}">{name}: {desc}</option>
// </Test::select>
// XML);
