<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testAddition()
    {
        $value = true;
        $array = [
            "key" => "value"
        ];
        $this->assertEquals(5, 3 + 2, "Five was expected to equal 2 plus 3 !");
        $this->assertTrue($value);
        $this->assertArrayHasKey("key", $array);
        $this->assertEquals("value", $array["key"]);
        $this->assertCount(1, $array);
    }
}