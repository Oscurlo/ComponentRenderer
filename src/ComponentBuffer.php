<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

class ComponentBuffer extends ComponentInterpreter implements Rendering
{
    /**
     * Start buffering
     * 
     * @param string|array $components
     * @return self
     */
    public function start(): self
    {
        ob_start();
        return $this;
    }

    public function render(string $html): self
    {
        return self::print(self::interpreter($html));
    }

    public function print(string $string): self
    {
        echo $string;
        return $this;
    }

    /**
     * End output buffering
     * 
     * @return self
     */
    public function end(): self
    {
        $result = self::interpreter(ob_get_contents());
        ob_end_clean();

        return self::print($result);
    }
}
