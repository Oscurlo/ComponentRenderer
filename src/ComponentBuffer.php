<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

class ComponentBuffer extends ComponentInterpreter
{
    /**
     * Start buffering
     * 
     * @param string|array $components
     * @return void
     */
    public function start(): void
    {
        ob_start();
    }

    /**
     * End output buffering
     * 
     * @return void
     */
    public function end(): void
    {
        $result = self::interpreter(ob_get_contents());
        ob_end_clean();

        echo $result;
    }
}
