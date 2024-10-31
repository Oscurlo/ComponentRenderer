<?php

use Oscurlo\ComponentRenderer\Component;
use Oscurlo\ComponentRenderer\Examples\Components\Bootstrap;

Component::register_component([
    # 1. With path
    dirname(__DIR__) . "\\components" => "Layout",
    # 2. with namespace
    "Oscurlo\\ComponentRenderer\\Examples\\Components" => ["Container", "Row", "Column", "InputField", "TextareaField"],
        # 3. with references class
    Bootstrap::class => "card"
]);