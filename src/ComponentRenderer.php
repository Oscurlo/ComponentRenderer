<?php declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use DOMDocument;

final class ComponentRenderer extends ComponentBuffer
{
    # ComponentRenderer 
    # ComponentExecutor 
    # ComponentBuffer 
    # ComponentInterpreter 
    # ComponentManager ✔️
    public function __construct(?string $source = null)
    {
        parent::__construct();

        if (!empty($source)) {
            self::set_component_path($source);
        }
    }
}
