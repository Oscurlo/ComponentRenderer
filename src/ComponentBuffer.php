<?php declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use Exception;

class ComponentBuffer extends ComponentInterpreter
{
    /**
     * Start buffering
     * 
     * @param string|array $components
     * @return void
     */
    public function start(string|array $components): void
    {
        if (!$this->component_path_is_defined) {
            throw new Exception("Path for components is not defined.");
        }

        ob_start(fn($content) => $this->interpreter($content, $components));
    }

    /**
     * End output buffering
     * 
     * @return void
     */
    public function end(): void
    {
        ob_end_flush();
    }
}
