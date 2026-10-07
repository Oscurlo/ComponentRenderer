<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Examples\Components;

use Oscurlo\ComponentRenderer\Component;

/**
 * Loads SVG files as components (see example7.php).
 *
 * The component name is the file name: <Svg::star /> renders assets/svg/star.svg.
 * It relies on __call() / __callStatic(), so no dedicated method per icon is needed.
 */
final class Svg
{
    public function __call(string $method, array $params): string
    {
        return self::onCall($method, $params);
    }

    public static function __callStatic(string $method, array $params): string
    {
        return self::onCall($method, $params);
    }

    /**
     * @param  string $filename SVG file name (without extension)
     * @param  array  $params   Call arguments; the first one is the props object
     * @return string
     */
    private static function onCall(string $filename, array $params): string
    {
        $props = $params[0];

        $props->width ??= "24";
        $props->height ??= "24";

        $pathSvg = __DIR__ . "/../assets/svg/{$filename}.svg";

        // Unknown names fall back to the default icon
        if (!file_exists($pathSvg)) {
            $pathSvg = __DIR__ . "/../assets/svg/default.svg";
        }

        return Component::render(
            Component::template(
                filename: $pathSvg,
                props: $props,
            ),
        );
    }
}
