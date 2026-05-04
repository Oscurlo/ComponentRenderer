<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Tests\Feature;

use Oscurlo\ComponentRenderer\Component;
use Oscurlo\ComponentRenderer\ComponentRenderer;
use PHPUnit\Framework\TestCase;

/**
 * Inline components used only in tests — no file dependency needed.
 */
function Alert(object $props): string
{
    $type = $props->type ?? "info";
    return "<div class=\"alert alert-{$type}\">{$props->children}</div>";
}

function Badge(object $props): string
{
    return "<span class=\"badge\">{$props->textContent}</span>";
}

class RenderTest extends TestCase
{
    private const NAMESPACE = "Oscurlo\\ComponentRenderer\\Tests\\Feature";

    // -------------------------------------------------------------------------
    // Component::render — static API
    // -------------------------------------------------------------------------

    public function test_renders_simple_function_component(): void
    {
        $html = "<Alert type=\"danger\">Something went wrong</Alert>";

        $result = Component::render($html, [
            self::NAMESPACE => ["Alert"],
        ]);

        $this->assertStringContainsString("alert-danger", $result);
        $this->assertStringContainsString("Something went wrong", $result);
        $this->assertStringNotContainsString("<Alert", $result);
    }

    public function test_renders_component_with_text_content(): void
    {
        $html = "<Badge>99+</Badge>";

        $result = Component::render($html, [
            self::NAMESPACE => ["Badge"],
        ]);

        $this->assertStringContainsString("99+", $result);
        $this->assertStringContainsString("badge", $result);
        $this->assertStringNotContainsString("<Badge", $result);
    }

    public function test_renders_nested_components(): void
    {
        $html = "<Alert type=\"info\"><Badge>New</Badge></Alert>";

        $result = Component::render($html, [
            self::NAMESPACE => ["Alert", "Badge"],
        ]);

        $this->assertStringContainsString("alert-info", $result);
        $this->assertStringContainsString("badge", $result);
        $this->assertStringContainsString("New", $result);
        $this->assertStringNotContainsString("<Alert", $result);
        $this->assertStringNotContainsString("<Badge", $result);
    }

    public function test_returns_html_unchanged_when_no_components_registered(): void
    {
        $html = "<p>Hello world</p>";

        $result = Component::render($html);

        $this->assertStringContainsString("Hello world", $result);
    }

    public function test_unknown_tag_is_left_untouched(): void
    {
        // UnknownTag is NOT registered — should stay as-is
        $html = "<p>Hello</p><UnknownTag />";

        $result = Component::render($html, [
            self::NAMESPACE => ["Alert"],
        ]);

        $this->assertStringContainsString("<p>Hello</p>", $result);
    }

    // -------------------------------------------------------------------------
    // ComponentRenderer — instance API
    // -------------------------------------------------------------------------

    public function test_renderer_instance_renders_html(): void
    {
        $renderer = new ComponentRenderer([
            self::NAMESPACE => ["Alert"],
        ]);

        ob_start();
        $renderer->render("<Alert type=\"warning\">Watch out</Alert>");
        $output = ob_get_clean();

        $this->assertStringContainsString("alert-warning", $output);
        $this->assertStringContainsString("Watch out", $output);
    }

    public function test_renderer_accepts_components_on_render_call(): void
    {
        $renderer = new ComponentRenderer();

        ob_start();
        $renderer->render("<Badge>7</Badge>", [
            self::NAMESPACE => ["Badge"],
        ]);
        $output = ob_get_clean();

        $this->assertStringContainsString("badge", $output);
        $this->assertStringContainsString("7", $output);
    }

    // -------------------------------------------------------------------------
    // ComponentRenderer buffer API
    // -------------------------------------------------------------------------

    public function test_buffer_start_end_renders_html(): void
    {
        $renderer = new ComponentRenderer([
            self::NAMESPACE => ["Alert"],
        ]);

        ob_start();
        $renderer->start();
        echo "<Alert type=\"success\">Done!</Alert>";
        $renderer->end();
        $output = ob_get_clean();

        $this->assertStringContainsString("alert-success", $output);
        $this->assertStringContainsString("Done!", $output);
    }

    // -------------------------------------------------------------------------
    // Class-based component (static method)
    // -------------------------------------------------------------------------

    public function test_renders_static_class_method_component(): void
    {
        $html = "<StubCard title=\"Hello\">Body text</StubCard>";

        $result = Component::render($html, [
            StubCard::class => "render",
        ]);

        $this->assertStringContainsString("Hello", $result);
        $this->assertStringContainsString("Body text", $result);
        $this->assertStringNotContainsString("<StubCard", $result);
    }

    // -------------------------------------------------------------------------
    // Component::template
    // -------------------------------------------------------------------------

    public function test_template_throws_on_missing_file(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/Template not found/");

        Component::template("/non/existent/file.php");
    }

    public function test_template_renders_vars(): void
    {
        $file = tempnam(sys_get_temp_dir(), "cr_test_") . ".php";
        file_put_contents($file, "Hello {{ \$name }}!");

        $result = Component::template($file, ["name" => "World"]);

        unlink($file);

        $this->assertSame("Hello World!", $result);
    }

    public function test_template_renders_php_block_with_at_syntax(): void
    {
        $file = tempnam(sys_get_temp_dir(), "cr_test_") . ".php";
        file_put_contents($file, "{{ @\$x = 'works'; }}Result: {{ \$x }}");

        $result = Component::template($file);

        unlink($file);

        $this->assertStringContainsString("Result: works", $result);
    }
}

// ---------------------------------------------------------------------------
// Stub class-based component — defined at bottom to keep tests readable
// ---------------------------------------------------------------------------

final class StubCard
{
    public static function render(object $props): string
    {
        $title = $props->title ?? "";
        return "<div class=\"card\"><h2>{$title}</h2><p>{$props->children}</p></div>";
    }
}
