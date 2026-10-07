<?php

/**
 * Example 7 — Dynamic components with __call()
 *
 * A component does not need a dedicated method. If the registered class
 * implements __call() / __callStatic(), every name you register for it is
 * routed there, and the name arrives as the first argument.
 *
 * Here the Svg class treats the component name as a file name:
 * <Svg::star /> renders assets/svg/star.svg, <Svg::heart /> renders heart.svg…
 * Adding an icon means dropping a file in assets/svg/ — no new PHP code.
 * Names without a matching file fall back to assets/svg/default.svg.
 *
 * Best for: icon sets, view loaders, proxies — any group of components
 * that share the same logic.
 */

declare(strict_types=1);

use Oscurlo\ComponentRenderer\ComponentRenderer;
use Oscurlo\ComponentRenderer\Examples\Components\Svg;

include_once "../vendor/autoload.php";

$render = new ComponentRenderer();

$render->set_component_manager([
    // Every name registered here is handled by Svg::__call()
    Svg::class => ["default", "star", "heart"],
]);

$render->render(<<<HTML
<div style="display: flex; gap: 16px; align-items: center; padding: 16px;">
    <Svg::default width="48" height="48" />
    <Svg::star width="48" height="48" />
    <Svg::heart width="48" height="48" />
    <Svg::star />
</div>
HTML);
