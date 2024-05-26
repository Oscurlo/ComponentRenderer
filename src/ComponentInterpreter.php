<?php declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

class ComponentInterpreter extends ComponentExecutor
{
    protected function interpreter(string $html, string|array $components): string
    {
        foreach ($components as $component) {
            if (self::component_exists($component)) {
                $html = self::prepare($component, $html);
            }
        }

        return $html;
    }

    protected function prepare(string $component, string $subject): array|string|null
    {
        return preg_replace_callback_array([
            '|(<' . $component . '></' . $component . '>)|s' => fn($matches) => self::callback_component_execution($matches, $component),
            '|(<' . $component . ' .*?>.*?</' . $component . '>)|s' => fn($matches) => self::callback_component_execution($matches, $component),
        ], $subject);
    }

    protected function callback_component_execution(array $matches, string $component)
    {
        return self::execute_component($component, $matches[0]);
    }
}
