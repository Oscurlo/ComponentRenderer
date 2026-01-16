<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use Exception;

final class Component extends ComponentInterpreter
{
    # Option 3: Pass the html directly and return the content

    /**
     * Render and return content
     *
     * @param  string $html
     * @param  ?array $components
     * @return string
     */
    public static function render(string $html, ?array $components = null): string
    {
        $self = new self();

        if ($components) {
            $self->set_component_manager(
                $components
            );
        }

        return $self->interpreter($html);
    }

    /**
     * Rendering template
     *
     * @param  string      $filename
     * @param  array|null  $vars
     * @param  object|null $props
     * @return string
     * @throws Exception
     */
    public static function template(string $filename, array|null $vars = null, object|null $props = null): string
    {
        if (file_exists($filename)) {
            $pattern = "/\{\{(.*?)\}\}/s";
            $callback = function (array $matches): string {
                $trim = fn (string &$string): string => $string = trim($string);
                [$all, $code] = $matches;

                if (str_starts_with($trim($code), "@")) {
                    $code = substr($code, 1);
                    return "<?php {$code} ?>";
                }

                return "<?= {$code} ?>";
            };
            $subject = file_get_contents($filename);


            if ($vars) {
                extract(
                    $vars
                );
            }

            ob_start();

            eval("?>" . preg_replace_callback($pattern, $callback, $subject));

            return ob_get_clean();
        }

        throw new Exception("Template not found");
    }
}
