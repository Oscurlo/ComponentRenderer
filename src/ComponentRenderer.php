<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

final class ComponentRenderer extends ComponentBuffer
{
    /**
     * For easier use, the construct also receives the folder where the components are located.
     * 
     * @param ?string $folder
     */
    public function __construct(?string $folder = null)
    {
        if (!empty($folder)) {
            self::set_component_path($folder);
        }
    }
}
