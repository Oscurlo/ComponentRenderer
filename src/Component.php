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
     * @param string $html
     * @param ?array $components
     * @return string
     */
    public static function render(string $html, ?array $components = null): string
    {
        $self = new self;

        if ($components) {
            $self->set_component_manager($components);
        }

        return $self->interpreter($html);
    }

    /**
     * Rendering template
     * 
     * @param string $filename
     * @param array $vars
     * @param object $props
     * @return string
     */
    public static function template(string $filename, array $vars = null, object $props = null): string
    {
        if (file_exists($filename)) {

            $pattern = "/\{\{(.*?)\}\}/s";
            $callback = function (array $matches) {
                [$all, $code] = $matches;

                if (strpos($code, "@") !== false) {
                    $code = str_replace("@", "", $code);
                    return "<?php {$code} ?>";
                }

                return "<?= {$code} ?>";
            };
            $subject = file_get_contents($filename);

            if ($vars) {
                extract($vars);
            }

            ob_start();
            eval ("?>" . preg_replace_callback($pattern, $callback, $subject));
            $content = ob_get_clean();

            return $content;
        }

        throw new Exception("Template not found ¯\_(ツ)_/¯");
    }
}
