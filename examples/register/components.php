<?php

use Oscurlo\ComponentRenderer\Component;
use Oscurlo\ComponentRenderer\Examples\Components\Bootstrap;

Component::register_component(
    references: dirname(__DIR__) . "\\components",
    components: "Layout"
);

Component::register_component(
    references: "Oscurlo\\ComponentRenderer\\Examples\\Components",
    components: ["Container", "Row", "Column", "InputField", "TextareaField"]
);

Component::register_component(
    references: Bootstrap::class,
    components: "card"
);