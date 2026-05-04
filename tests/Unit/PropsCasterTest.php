<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Tests\Unit;

use Oscurlo\ComponentRenderer\ComponentInterface;
use Oscurlo\ComponentRenderer\PropsCaster;
use PHPUnit\Framework\TestCase;

class PropsCasterTest extends TestCase
{
    private function makeProps(array $data): object
    {
        return (object) $data;
    }

    // -------------------------------------------------------------------------
    // Boolean casting
    // -------------------------------------------------------------------------

    public function test_casts_string_true_to_bool(): void
    {
        $props = $this->makeProps(["active" => "true"]);
        PropsCaster::interface(["active" => "bool"], $props);

        $this->assertIsBool($props->active);
        $this->assertTrue($props->active);
    }

    public function test_casts_string_false_to_bool(): void
    {
        $props = $this->makeProps(["active" => "false"]);
        PropsCaster::interface(["active" => "boolean"], $props);

        $this->assertIsBool($props->active);
        $this->assertFalse($props->active);
    }

    public function test_casts_string_one_to_bool_true(): void
    {
        $props = $this->makeProps(["active" => "1"]);
        PropsCaster::interface(["active" => "bool"], $props);

        $this->assertTrue($props->active);
    }

    public function test_casts_string_zero_to_bool_false(): void
    {
        $props = $this->makeProps(["active" => "0"]);
        PropsCaster::interface(["active" => "bool"], $props);

        $this->assertFalse($props->active);
    }

    // -------------------------------------------------------------------------
    // Integer casting
    // -------------------------------------------------------------------------

    public function test_casts_string_to_int(): void
    {
        $props = $this->makeProps(["count" => "42"]);
        PropsCaster::interface(["count" => "int"], $props);

        $this->assertIsInt($props->count);
        $this->assertSame(42, $props->count);
    }

    public function test_casts_string_to_integer_alias(): void
    {
        $props = $this->makeProps(["count" => "7"]);
        PropsCaster::interface(["count" => "integer"], $props);

        $this->assertSame(7, $props->count);
    }

    // -------------------------------------------------------------------------
    // Float / double casting
    // -------------------------------------------------------------------------

    public function test_casts_string_to_float(): void
    {
        $props = $this->makeProps(["price" => "9.99"]);
        PropsCaster::interface(["price" => "float"], $props);

        $this->assertIsFloat($props->price);
        $this->assertSame(9.99, $props->price);
    }

    public function test_casts_string_to_double(): void
    {
        $props = $this->makeProps(["ratio" => "3.14"]);
        PropsCaster::interface(["ratio" => "double"], $props);

        $this->assertIsFloat($props->ratio);
    }

    // -------------------------------------------------------------------------
    // String casting
    // -------------------------------------------------------------------------

    public function test_casts_int_to_string(): void
    {
        $props = $this->makeProps(["label" => 123]);
        PropsCaster::interface(["label" => "string"], $props);

        $this->assertIsString($props->label);
        $this->assertSame("123", $props->label);
    }

    public function test_casts_with_str_alias(): void
    {
        $props = $this->makeProps(["label" => 99]);
        PropsCaster::interface(["label" => "str"], $props);

        $this->assertSame("99", $props->label);
    }

    // -------------------------------------------------------------------------
    // Array casting
    // -------------------------------------------------------------------------

    public function test_casts_json_string_to_array(): void
    {
        $props = $this->makeProps(["items" => '["a","b","c"]']);
        PropsCaster::interface(["items" => "array"], $props);

        $this->assertIsArray($props->items);
        $this->assertSame(["a", "b", "c"], $props->items);
    }

    // -------------------------------------------------------------------------
    // Default / unknown type — value unchanged
    // -------------------------------------------------------------------------

    public function test_unknown_type_leaves_value_unchanged(): void
    {
        $props = $this->makeProps(["foo" => "bar"]);
        PropsCaster::interface(["foo" => "unknown_type"], $props);

        $this->assertSame("bar", $props->foo);
    }

    // -------------------------------------------------------------------------
    // Missing prop — not touched
    // -------------------------------------------------------------------------

    public function test_missing_prop_is_ignored(): void
    {
        $props = $this->makeProps(["name" => "Esteban"]);
        PropsCaster::interface(["age" => "int"], $props);

        $this->assertFalse(property_exists($props, "age"));
        $this->assertSame("Esteban", $props->name);
    }

    // -------------------------------------------------------------------------
    // Multiple props at once
    // -------------------------------------------------------------------------

    public function test_casts_multiple_props_in_one_call(): void
    {
        $props = $this->makeProps([
            "active" => "true",
            "count"  => "5",
            "label"  => 10,
        ]);

        PropsCaster::interface([
            "active" => "bool",
            "count"  => "int",
            "label"  => "string",
        ], $props);

        $this->assertTrue($props->active);
        $this->assertSame(5, $props->count);
        $this->assertSame("10", $props->label);
    }

    // -------------------------------------------------------------------------
    // Backwards compatibility — ComponentInterface still works as alias
    // -------------------------------------------------------------------------

    public function test_component_interface_is_alias_of_props_caster(): void
    {
        $this->assertTrue(is_a(ComponentInterface::class, PropsCaster::class, true));
    }

    public function test_component_interface_casts_correctly(): void
    {
        $props = $this->makeProps(["count" => "3"]);
        ComponentInterface::interface(["count" => "int"], $props);

        $this->assertSame(3, $props->count);
    }
}
