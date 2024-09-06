<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

final class Component extends ComponentInterpreter implements Rendering
{
    public function render(string $html, ?array $components = null): string
    {
        if (!empty($components)) self::set_component_manager($components);
        return self::interpreter($html);
    }
}
