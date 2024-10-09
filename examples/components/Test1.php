<?php

declare(strict_types=1);

namespace Examples\Components;

use Oscurlo\ComponentRenderer\Component;

final class Test1
{
    public int $i = 0;
    public array $options = [
        [
            "id" => 0,
            "name" => "Yūji Itadori",
            "desc" => "Esse do minim deserunt deserunt officia irure cillum eu ipsum elit cupidatat incididunt et."
        ],
        [
            "id" => 1,
            "name" => "Megumi Fushiguro",
            "desc" => "Amet aute sunt minim Lorem eiusmod ad est."
        ],
        [
            "id" => 2,
            "name" => "Satoru Gojō",
            "desc" => "Enim quis excepteur enim magna velit elit in incididunt."
        ]
    ];

    public function select(object $props)
    {
        $options = [];

        foreach ($this->options as $i => $option) {
            $options[$i] = $props->children;
            foreach ($option as $key => $value) {
                $options[$i] = str_replace("{{$key}}", (string) $value, $options[$i]);
            }
        }

        $options = implode(PHP_EOL, $options);

        $attrs = Component::get_attributes($props);

        return Component::render(<<<HTML
        <select {$attrs}>
            {$options}
        </select>
        HTML);
    }
}
