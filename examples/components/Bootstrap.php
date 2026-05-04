<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Examples\Components;

use Exception;
use Oscurlo\ComponentRenderer\Component;

final class Bootstrap
{
    public static function card(object $props): string
    {
        $props->{"card-title"} ??= false;
        $props->{"card-footer"} ??= false;

        $filename = __DIR__ . "/../templates/card.php";

        return Component::render(
            Component::template(filename: $filename, props: $props),
        );
    }

    public function accordion(object $props): string
    {
        $id = $props->{"id"} ?? "";
        $textContent = $props->{"textContent"} ?? "";
        $indexCollapse = $props->{"index-collapse"} ?? "";

        if (!$id) {
            throw new Exception("ID is required");
        }

        $result = "";

        $inCheck = fn(bool $check, mixed $yes, mixed $no) => $check
            ? $yes
            : $no;
        $encode = fn(string $string) => base64_encode($string);

        $jsonData = json_decode($textContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON no is valid");
        }

        $newAccordion = function (
            array $info,
            int $index,
            int $chekIndex = 0,
        ) use ($inCheck, $encode, $id) {
            $btnClass = $inCheck($index === $chekIndex, "", "collapsed");
            $expanded = $inCheck($index === $chekIndex, "true", "false");
            $show = $inCheck($index === $chekIndex, "show", "");
            $collapseId = "collapse{$encode("$index")}";

            return <<<HTML
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button {$btnClass}" type="button" data-bs-toggle="collapse" data-bs-target="#{$collapseId}" aria-expanded="{$expanded}" aria-controls="{$collapseId}">
                        {$info["title"]}
                    </button>
                </h2>
                <div id="{$collapseId}" class="accordion-collapse collapse {$show}" data-bs-parent="#{$id}">
                    <div class="accordion-body">
                        {$info["body"]}
                    </div>
                </div>
            </div>
            HTML;
        };

        foreach ($jsonData as $i => $data) {
            $result .= $newAccordion($data, $i, (int) $indexCollapse) ?: 0;
        }

        return <<<HTML
        <div class="accordion" id="{$id}">
            {$result}
        </div>
        HTML;
    }
}
