<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

class ComponentBuffer extends ComponentInterpreter
{
    # Option 2: Use the buffer to capture the html

    /**
     * Start buffering
     * 
     * @return self
     */
    public function start(): self
    {
        ob_start();
        return $this;
    }

    /**
     * End output buffering and display content
     * 
     * @return void
     */
    public function end(): void
    {
        $result = self::interpreter(ob_get_contents());
        ob_end_clean();

        self::print($result);
    }
}
