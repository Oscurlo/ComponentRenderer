<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use Oscurlo\ComponentRenderer\Contracts\RendererInterface;

/**
 * Main entry point — instance-based API.
 *
 * Usage:
 *   $renderer = new ComponentRenderer(['MyNS' => ['MyComponent']]);
 *   $renderer->render('<MyComponent />');
 *
 *   // Or with output buffering:
 *   $renderer->start();
 *   echo '<MyComponent />';
 *   $renderer->end();
 */
final class ComponentRenderer extends ComponentBuffer implements RendererInterface
{
    /**
     * Optionally register components at construction time.
     *
     * @param array<string, string|array<int, string>>|null $components
     */
    public function __construct(?array $components = null)
    {
        if ($components) {
            $this->set_component_manager($components);
        }
    }

    /**
     * Render and print the given HTML, optionally registering extra components.
     *
     * @param  string                                        $html
     * @param  array<string, string|array<int, string>>|null $components
     * @return void
     */
    public function render(string $html, ?array $components = null): void
    {
        if ($components) {
            $this->set_component_manager($components);
        }

        self::print($this->interpreter($html));
    }
}
