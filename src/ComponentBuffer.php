<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

class ComponentBuffer extends ComponentInterpreter
{
    # Option 2: Use the buffer to capture the html

    /**
     * Start buffering
     *
     * @return void
     */
    public function start(): void
    {
        ob_start();
    }

    /**
     * End output buffering and display content
     * 
     * @return void
     */

    public function end(): void
    {
        self::print(
            self::interpreter(
                ob_get_clean()
            )
        );
    }
}
