<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use Oscurlo\ComponentRenderer\Concerns\RendersOutput;

/**
 * Provides output buffering API: start() / end().
 * The HTML captured between those calls is passed through the interpreter.
 */
class ComponentBuffer extends ComponentInterpreter
{
    use RendersOutput;
}
