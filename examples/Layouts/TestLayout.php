<?php

declare(strict_types=1);

namespace Examples\Layouts;

use Oscurlo\ComponentRenderer\Component;

final class TestLayout
{
    static function system(object $props): string
    {
        $props->title ??= "ComponentRenderer";

        $props->lang ??= "es";
        $props->charset ??= "UTF-8";

        $filename = dirname(__DIR__) . "/templates/test.blade.php";

        return Component::render(Component::template($filename, $props, ["test" => "que pasa crack"]));
    }
}
