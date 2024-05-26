<?php declare(strict_types=1);

namespace Oscurlo\ComponentRenderer;

use Exception;

class ComponentExecutor extends ComponentManager
{

    /**
     * Executing component
     * 
     * @param string $component
     * @param string $html
     * @return string
     */
    protected function execute_component(string $component, string $html): string
    {
        ["component" => $comp, "method" => $meth] = $this->split_component($component);
        $function = $meth ? "{$comp}::{$meth}" : $comp;

        if (($meth && !class_exists($comp)) || (!$meth && !function_exists($comp))) {
            include $this->get_component_file($component);
        }

        $params = $this->get_params($html, $function);

        if ($meth && class_exists($comp) && method_exists($comp, $meth)) {
            return $comp::$meth($params);
        } else if (!$meth && function_exists($comp)) {
            return $function($params);
        }

        throw new Exception("Component {$function} not found.");
    }

    /**
     * Get Params
     * 
     * @param string $html
     * @param string $component
     * @return array
     */
    protected function get_params(string $html, string $component): array
    {
        $html = str_replace($component, "object", $html);

        $attrs = [];

        if ($this->dom->loadHTML($html, LIBXML_NOERROR)) {
            $tag = $this->dom->getElementsByTagName("object")->item(0);

            if ($tag) {
                $attrs["children"] = "";
                $attrs["textContent"] = $tag->textContent;

                foreach ($tag->childNodes as $child) {
                    $attrs["children"] .= $this->dom->saveHTML($child);
                }

                foreach ($tag->attributes as $attribute) {
                    $attrs[$attribute->name] = $attribute->value;
                }
            }

        } else {
            $attrs["failed"] = "error";
        }

        return $attrs;
    }
}
