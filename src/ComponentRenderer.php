<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

final class ComponentRenderer extends ComponentBuffer
{
    public function __construct(?string $source = null)
    {
        if (!empty($source)) {
            self::set_component_path($source);
        }
    }
}
