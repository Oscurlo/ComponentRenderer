<?php

declare(strict_types=1);

use Oscurlo\ComponentRenderer\Component;

# This's a regular example

class Bootstrap
{
    static function card(object $prop): string
    {
        $components = [
            dirname(__DIR__) . "/Logic" => "YES"
        ];

        return (new Component)->render(<<<HTML
        <div class="card">
            <YES condition="{$prop->title}"><div class="card-header">{$prop->title}</div></YES>
            <div class="card-body">{$prop->children}</div>
            <YES condition="{$prop->footer}"><div class="card-footer">{$prop->footer}</div></YES>
        </div>
        HTML, $components);
    }

    static function accordion(object $prop): string
    {
        [
            "id" => $id,
            "textContent" => $textContent,
            "index-collapse" => $indexCollapse
        ] = (array)$prop;

        if (!$id) throw new Exception("ID is required");

        $result = "";

        $inCheck = fn(bool $check, mixed $yes, mixed $no) => $check ? $yes : $no;
        $encode = fn(string $string) => base64_encode($string);

        $jsonData = json_decode($textContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) throw new Exception("JSON no is valid");

        $newAccordion = fn(array $info, int $index, int $chekIndex = 0) => <<<HTML
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button {$inCheck($index ===$chekIndex, '', 'collapsed')}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{$encode('$index')}"
                    aria-expanded="{$inCheck($index ===$chekIndex, 'true', 'false')}" aria-controls="collapse{$encode('$index')}">
                    {$info["title"]}
                </button>
            </h2>
            <div id="collapse{$encode('$index')}" class="accordion-collapse collapse {$inCheck($index ===$chekIndex, 'show', '')}" data-bs-parent="#{$id}">
                <div class="accordion-body">
                    {$info["body"]}
                </div>
            </div>
        </div>
        HTML;

        foreach ($jsonData as $i => $data)
            $result .= $newAccordion($data, $i, (int) $indexCollapse) ?: 0;

        return <<<HTML
        <div class="accordion" id="{$id}">
            {$result}
        </div>
        HTML;
    }
}
