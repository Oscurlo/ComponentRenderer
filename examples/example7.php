<?php

/**
 * Example 7 — Dinamic
 *
 * Que tal si nos ponemos creativos?
 * 
 * Este componente usa el metodo magico de __call para no tener que tener que depender de un metodo existente para funcionar
 * 
 * Para este caso quiero cargar svg(s) y el funcionamiendo para todos es el mismo le indico el svg que quiero cargar como un componente y el funcionamiento es el mismo
 */

declare(strict_types=1);

use Oscurlo\ComponentRenderer\ComponentRenderer;
use Oscurlo\ComponentRenderer\Examples\Components\Svg;

include_once "../vendor/autoload.php";

$render = new ComponentRenderer();

$render->set_component_manager([
    Svg::class => "default"
]);

$render->render(<<<HTML
<Svg::default width="100" height="100" />
HTML);
