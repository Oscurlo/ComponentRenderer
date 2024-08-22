<?php

# This's a regular example

/**
 * Componente de prueba: Este componente es mas par ayudar un poco al SEO agregando el ancho y alto a las imagees
 * 
 * @param array $main contiene valores que requiere el componente
 * 
 * @return string Retorna el html
 */

declare(strict_types=1);

use Oscurlo\ComponentRenderer\ComponentManager;

function Image(array $main): string
{
    @[
        "src" => $src
    ] = $main;

    $attrs = ComponentManager::extract_attributes(
        $main,
        array_filter(
            array_keys($main),
            fn($col) => !in_array($col, ["children", "textContent"])
        )
    );

    $html = "<img {$attrs}>";

    if (!empty($src) && file_exists($src)) {
        [$width, $height] = getimagesize($src);

        $dom = new DOMDocument(
            encoding: "UTF_8"
        );

        $dom->loadHTML(ComponentManager::html_base($html));

        $img = $dom->getElementsByTagName("img")->item(0);

        $img->setAttribute("width", (string)$width);
        $img->setAttribute("height", (string)$height);
        $img->setAttribute("loading", "lazy");

        $html = ComponentManager::get_body(
            html: $dom->saveHTML(),
            encoding: "UTF_8"
        );
    }

    return $html;
}
