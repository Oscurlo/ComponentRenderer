<?php

/**
 * Functions with "namespace"
 * If you use "namespace" with the functions you will be able to access all of them
 * Example: examples\components\Bootstrap.php -> Oscurlo\\ComponentRenderer\\Examples\\Components
 */

declare(strict_types=1);

use Oscurlo\ComponentRenderer\Examples\Components\Bootstrap;
use Oscurlo\ComponentRenderer\{Component, ComponentRenderer};

include_once "../vendor/autoload.php";

$render = new ComponentRenderer;

$render->set_component_manager([
    __DIR__ . "\\components" => "Layout",
    "Oscurlo\\ComponentRenderer\\Examples\\Components" => ["Container", "Row", "Column", "InputField"],
    Bootstrap::class => "card"
]);

$render->render(
    Component::template(
        __DIR__ . "/templates/example.blade.php"
    )
);