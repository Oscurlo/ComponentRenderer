<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

final class ComponentRenderer extends ComponentBuffer
{
    /**
     * For easier use, the construct also receives the folder where the components are located.
     * 
     * @param ?array $folder
     */
    public function __construct(?array $folder = null)
    {
        parent::__construct();
        if (!empty($folder)) self::set_component_manager($folder);
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
        if (!empty($components)) self::set_component_manager($components);
        self::print(self::interpreter($html));
    }
}
