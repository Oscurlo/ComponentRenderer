<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Tests\Unit;

use Oscurlo\ComponentRenderer\Support\HtmlHelper;
use PHPUnit\Framework\TestCase;

class HtmlHelperTest extends TestCase
{
    // -------------------------------------------------------------------------
    // HtmlHelper::wrap
    // -------------------------------------------------------------------------

    public function test_wrap_produces_html_document_structure(): void
    {
        $result = HtmlHelper::wrap("<p>Hello</p>");

        $this->assertStringContainsString("<html", $result);
        $this->assertStringContainsString("<body>", $result);
        $this->assertStringContainsString("<p>Hello</p>", $result);
        $this->assertStringContainsString("</html>", $result);
    }

    public function test_wrap_uses_default_utf8_encoding(): void
    {
        $result = HtmlHelper::wrap("<p>Hello</p>");

        $this->assertStringContainsString("UTF-8", $result);
    }

    public function test_wrap_uses_custom_encoding(): void
    {
        $result = HtmlHelper::wrap("<p>Hello</p>", "ISO-8859-1");

        $this->assertStringContainsString("ISO-8859-1", $result);
    }

    public function test_wrap_preserves_fragment_content(): void
    {
        $fragment = '<div class="foo"><span>Bar</span></div>';
        $result = HtmlHelper::wrap($fragment);

        $this->assertStringContainsString($fragment, $result);
    }

    // -------------------------------------------------------------------------
    // HtmlHelper::unwrap
    // -------------------------------------------------------------------------

    public function test_unwrap_extracts_body_content(): void
    {
        $html = "<html><body><p>Hello</p></body></html>";
        $result = HtmlHelper::unwrap($html);

        $this->assertStringContainsString("<p>Hello</p>", $result);
        $this->assertStringNotContainsString("<html>", $result);
        $this->assertStringNotContainsString("<body>", $result);
    }

    public function test_unwrap_returns_original_when_no_body_found(): void
    {
        // An empty string produces no body content → fallback to original
        $result = HtmlHelper::unwrap("");

        $this->assertSame("", $result);
    }

    public function test_unwrap_handles_multiple_children(): void
    {
        $html = "<html><body><p>One</p><p>Two</p></body></html>";
        $result = HtmlHelper::unwrap($html);

        $this->assertStringContainsString("<p>One</p>", $result);
        $this->assertStringContainsString("<p>Two</p>", $result);
    }

    public function test_wrap_then_unwrap_roundtrip(): void
    {
        $fragment = "<p>Hello world</p>";
        $wrapped = HtmlHelper::wrap($fragment);
        $unwrapped = HtmlHelper::unwrap($wrapped);

        $this->assertStringContainsString("Hello world", $unwrapped);
        $this->assertStringNotContainsString("<html", $unwrapped);
    }

    // -------------------------------------------------------------------------
    // HtmlHelper::isDocument
    // -------------------------------------------------------------------------

    public function test_is_document_returns_true_for_full_html(): void
    {
        $html = "<html lang=\"en\"><body><p>Hi</p></body></html>";

        $this->assertTrue(HtmlHelper::isDocument($html));
    }

    public function test_is_document_returns_false_for_fragment(): void
    {
        $this->assertFalse(HtmlHelper::isDocument("<p>Hello</p>"));
        $this->assertFalse(
            HtmlHelper::isDocument("<div><span>foo</span></div>"),
        );
        $this->assertFalse(HtmlHelper::isDocument(""));
    }

    public function test_is_document_detects_wrapped_output(): void
    {
        $wrapped = HtmlHelper::wrap("<p>Test</p>");

        $this->assertTrue(HtmlHelper::isDocument($wrapped));
    }
}
