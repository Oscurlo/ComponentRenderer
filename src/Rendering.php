<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

interface Rendering
{
    public function render(string $html): self|string;
}
