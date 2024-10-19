<?php

/**
 * You can also register components in an external file and include it when you are going to use it.
 * Example: examples\register\components.php
 */

declare(strict_types=1);

use Oscurlo\ComponentRenderer\{Component, ComponentRenderer};

include_once "../vendor/autoload.php";
include_once "./register/components.php";

$render = new ComponentRenderer;

$render->render(
    Component::template(
        __DIR__ . "/templates/example.blade.php"
    )
);