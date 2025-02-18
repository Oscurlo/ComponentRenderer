<?php

declare(strict_types=1);

namespace Oscurlo\ComponentRenderer\Tests;

use Oscurlo\ComponentRenderer\ComponentInterface;
use PHPUnit\Framework\TestCase;

final class ComponentInterfaceTest extends TestCase
{
    private ComponentInterface $test;

    protected function setUp(): void
    {
        $this->test = new ComponentInterface;
    }

    public function testBoolean(): void
    {
        $props = (object) [];

        # true
        $props->test1 = "1";
        $props->test2 = "true";
        $props->test3 = "on";
        $props->test4 = "yes";

        # false
        $props->test5 = "0";
        $props->test6 = "false";
        $props->test7 = "off";
        $props->test8 = "no";

        $this->test::interface([
            "test1" => "boolean",
            "test2" => "boolean",
            "test3" => "boolean",
            "test4" => "boolean",
            "test5" => "boolean",
            "test6" => "boolean",
            "test7" => "boolean",
            "test8" => "boolean"
        ], $props);

        for ($i = 1; $i <= 8; $i++) {
            self::{$i <= 4 ? "assertTrue" : "assertFalse"}(
                condition: $props->{"test{$i}"}
            );
        }
    }
}