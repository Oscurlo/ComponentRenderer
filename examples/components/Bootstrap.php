<?php

declare(strict_types=1);

# This's a regular example

class Bootstrap
{
    static function card(object $main): string
    {
        [
            "children" => $children,
            "title" => $title
        ] = (array)$main;

        return <<<HTML
        <div class="card">
            <div class="card-header">{$title}</div>
            <div class="card-body">{$children}</div>
        </div>
        HTML;
    }

    static function accordion(object $main): string
    {
        [
            "children" => $children,
            "textContent" => $textContent,
            "id" => $id,
            "index-collapse" => $indexCollapse
        ] = (array)$main;

        $result = "";

        $inCheck = fn(bool $check, mixed $yes, mixed $no) => $check ? $yes : $no;
        $encode = fn(string $string) => base64_encode($string);

        $jsonData = json_decode($textContent, true);

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

        <div>
            Lorem, ipsum dolor.
        </div>
        HTML;
    }
}
