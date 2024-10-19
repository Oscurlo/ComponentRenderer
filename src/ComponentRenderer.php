<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

final class ComponentRenderer extends ComponentBuffer
{
    /**
     * For easier use, the construct also receives the folder where the components are located.
     *
     * @param array|null $components
     */
    public function __construct(?array $components = null)
    {
        if ($components) {
            self::set_component_manager(
                $components
            );
        }
    }

    # Option 1: Pass the html directly and display the content

    /**
     * Render and display content
     * 
     * @param string $html
     * @param ?array $components
     * @return void
     */
    public function render(string $html, ?array $components = null): void
    {
        if ($components) {
            self::set_component_manager(
                $components
            );
        }

        self::print(
            self::interpreter(
                $html
            )
        );
    }
}
