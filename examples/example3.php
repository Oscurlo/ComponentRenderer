<?php

/**
 * If you want to pass variables to the template, you can use the `template` method.
 */

declare(strict_types=1);

use Oscurlo\ComponentRenderer\Component;
use Oscurlo\ComponentRenderer\ComponentRenderer;
use Oscurlo\ComponentRenderer\Examples\Components\Bootstrap;

include_once "../vendor/autoload.php";

$render = new ComponentRenderer();

$render->set_component_manager([
    __DIR__ . "\\components" => "Layout",
    "Oscurlo\\ComponentRenderer\\Examples\\Components" => [
        "Container",
        "Row",
        "Column",
        "InputField",
    ],
    Bootstrap::class => "card",
]);

$users = [
    ["id" => 1, "name" => "Freddie Mercury"],
    ["id" => 2, "name" => "Mike Tyson"],
    ["id" => 3, "name" => "Michael Jackson"],
];

$render->render(
    Component::template(__DIR__ . "/templates/example.php", [
        // Set variables for the template
        "users" => $users,
    ]),
);
