<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

final class Component extends ComponentInterpreter
{
    # Option 3: Pass the html directly and return the content

    /**
     * Render and return content
     * 
     * @param string $html
     * @param ?array $components
     * @return string
     */
    public static function render(string $html, ?array $components = null): string
    {
        $self = new self;

        if (!empty($components)) $self->set_component_manager($components);
        return $self->interpreter($html);
    }
}
