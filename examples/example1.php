<?php

/**
 * Functions with "namespace"
 * If you use "namespace" with the functions you will be able to access all of them
 * Example: examples\components\Bootstrap.php -> Oscurlo\\ComponentRenderer\\Examples\\Components
 */

declare(strict_types=1);

use Oscurlo\ComponentRenderer\Component;
use Oscurlo\ComponentRenderer\ComponentRenderer;
use Oscurlo\ComponentRenderer\Examples\Components\Bootstrap;

include_once "../vendor/autoload.php";

$render = new ComponentRenderer();

$render->set_component_manager([
    // Register components from the "components" directory
    __DIR__ . "\\components" => "Layout",

    // Register components from the "Oscurlo\\ComponentRenderer\\Examples\\Components" namespace
    "Oscurlo\\ComponentRenderer\\Examples\\Components" => [
        "Container",
        "Row",
        "Column",
        "InputField",
    ],

    // Register the Bootstrap component from the "Oscurlo\\ComponentRenderer\\Examples\\Components" namespace
    Bootstrap::class => "card",
]);

$render->render(Component::template(__DIR__ . "/templates/example.php"));
