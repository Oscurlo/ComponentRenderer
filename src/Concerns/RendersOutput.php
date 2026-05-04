<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Concerns;

/**
 * Provides output buffering helpers for capturing and printing rendered HTML.
 * Used by ComponentBuffer and ComponentRenderer.
 */
trait RendersOutput
{
    /**
     * Echo one or more strings separated by a space.
     *
     * @param  string ...$expressions
     * @return void
     */
    public static function print(string ...$expressions): void
    {
        echo implode(" ", $expressions);
    }

    /**
     * Start output buffering.
     *
     * @return void
     */
    public function start(): void
    {
        ob_start();
    }

    /**
     * Flush the output buffer through the interpreter and print the result.
     *
     * @return void
     */
    public function end(): void
    {
        self::print(
            $this->interpreter((string) ob_get_clean())
        );
    }
}
