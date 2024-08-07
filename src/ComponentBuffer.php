<?php

declare(strict_types=1);

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
        if (!$this->component_path_is_defined) throw new Exception("The components folder is not defined");

        # To avoid validations I validate if it is not an array and convert it
        if (!is_array($components)) $components = [$components];

        ob_start(fn ($content) => self::interpreter(
            # To avoid problems with reading the html, I commented all the received components.
            self::comment_component(
                $content,
                $components
            ),
            $components
        ));
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
