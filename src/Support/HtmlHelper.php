<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Support;

use DOMDocument;
use DOMXPath;
use Tidy;

final class HtmlHelper
{
    /**
     * Generate a minimal HTML document wrapper around a fragment.
     * Used internally so DOMDocument can parse partial HTML reliably.
     *
     * @param  string $html     HTML fragment
     * @param  string $encoding Encoding type
     * @return string
     */
    public static function wrap(
        string $html,
        string $encoding = "UTF-8",
    ): string {
        return <<<HTML
        <html lang="en">
            <meta http-equiv="Content-Type" content="text/html;charset={$encoding}">
            <meta charset="{$encoding}">
            <body>{$html}</body>
        </html>
        HTML;
    }

    /**
     * Extract only the inner content of the <body> tag from a full HTML document.
     *
     * @param  string $html     Full HTML document
     * @param  string $version  DOMDocument version
     * @param  string $encoding DOMDocument encoding
     * @return string
     */
    public static function unwrap(
        string $html,
        string $version = "1.0",
        string $encoding = "UTF-8",
    ): string {
        if ($html === "") {
            return "";
        }

        $dom = new DOMDocument($version, $encoding);

        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $bodyNode = $xpath->query("//body")->item(0);
        $bodyContent = "";

        if ($bodyNode) {
            foreach ($bodyNode->childNodes as $child) {
                $bodyContent .= $dom->saveHTML($child);
            }
        }

        return $bodyContent !== "" ? $bodyContent : $html;
    }

    /**
     * Check whether the given string contains a full HTML document (i.e. has an <html> tag).
     *
     * @param  string $html
     * @return bool
     */
    public static function isDocument(string $html): bool
    {
        return (bool) preg_match("|<html(.*?)</html>|s", $html);
    }

    /**
     * Tidy the given HTML string using the Tidy extension.
     *
     * @param  string $html
     * @param  array $options
     * @return string
     */
    public static function tidy(string $html, array $options = []): string
    {
        if (!extension_loaded("tidy")) {
            return $html;
        }

        // Lo formateo como lo hace mi editor a 4 espacios
        $defaultOptions = [
            "indent" => true,
            "indent-spaces" => 4,
            "wrap" => 0,
        ];

        $tidy = new Tidy();
        $tidy->parseString($html, $defaultOptions + $options);
        $tidy->cleanRepair();

        return $tidy->value;
    }
}
