<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Contracts;

/**
 * Defines the public contract for any HTML component renderer.
 *
 * Implementations must be able to:
 *   - Accept a component map at construction or registration time.
 *   - Render an HTML string with component substitution.
 *   - Expose the current component map for introspection.
 */
interface RendererInterface
{
    /**
     * Register one or more components into the renderer.
     *
     * The array maps a reference (namespace, class, or directory path)
     * to a component name or list of names:
     *
     *   [
     *     'App\\Components'  => ['Button', 'Card'],
     *     '/path/to/views'   => ['Header'],
     *     App\UI\Form::class => 'input',
     *   ]
     *
     * @param  array<string, string|array<int, string>> $components
     * @return void
     */
    public function set_component_manager(array $components): void;

    /**
     * Return the currently registered component map, or null if empty.
     *
     * @return array<string, array<int, string>>|null
     */
    public function get_component_manager(): ?array;

    /**
     * Render the given HTML string, replacing registered component tags
     * with their rendered output, and print the result.
     *
     * @param  string                                         $html
     * @param  array<string, string|array<int, string>>|null  $components  Extra components for this call only
     * @return void
     */
    public function render(string $html, ?array $components = null): void;
}
