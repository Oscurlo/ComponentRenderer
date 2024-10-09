<?php

# This's a regular example

/**
 * Componente de prueba: Este componente es mas par ayudar un poco al SEO agregando el ancho y alto a las imagees
 * 
 * @param object $main contiene valores que requiere el componente
 * 
 * @return string Retorna el html
 */

declare(strict_types=1);

use Oscurlo\ComponentRenderer\ComponentManager;

function Image(object $main): string
{
    @[
        "src" => $src
    ] = (array) $main;

    $attrs = ComponentManager::get_attributes($main);

    $html = "<img {$attrs}>";

    if (!empty($src) && file_exists($src)) {
        [$width, $height] = getimagesize($src);

        $dom = new DOMDocument(
            encoding: "UTF_8"
        );

        $dom->loadHTML(ComponentManager::html_base($html));

        $img = $dom->getElementsByTagName("img")->item(0);

        $img->setAttribute("width", (string) $width);
        $img->setAttribute("height", (string) $height);
        $img->setAttribute("loading", "lazy");

        $html = ComponentManager::get_body(
            html: $dom->saveHTML(),
            encoding: "UTF_8"
        );
    }

    return $html;
}
