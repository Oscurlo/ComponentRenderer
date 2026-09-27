<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Examples\Components;

use Oscurlo\ComponentRenderer\Component;

final class Svg
{
    public function __call(string $method, mixed $params): string
    {
        return self::onCall($method, $params);
    }

    public static function __callStatic(string $method, mixed $params): string
    {
        return self::onCall($method, $params);
    }

    private static function onCall(string $filename, array $params)
    {
        $props = $params[0];

        $props->width ??= "24";
        $props->height ??= "24";

        $pathSvg = __DIR__ . "/../assets/svg/{$filename}.svg";

        if (!file_exists($pathSvg))
            $pathSvg = __DIR__ . "/../assets/svg/default.svg";

        return Component::render(
            Component::template(
                filename: $pathSvg,
                props: $props
            )
        );
    }
}
