<?php

namespace Pop\Css\Test;

use Pop\Css\Color;

class ColorTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateRgb()
    {
        $rgb = Color::rgb(255, 255, 255, 0.5);
        $this->assertInstanceOf('Pop\Css\Color\Rgb', $rgb);
    }

    public function testCreateHsl()
    {
        $hsl = Color::hsl(240, 100, 100, 0.5);
        $this->assertInstanceOf('Pop\Css\Color\Hsl', $hsl);
    }

    public function testCreateHex()
    {
        $hex = Color::hex('#fff');
        $this->assertInstanceOf('Pop\Css\Color\Hex', $hex);
    }

    public function testParseRgb()
    {
        $rgb = Color::parse('rgba(255, 255, 255, 0.5)');
        $this->assertInstanceOf('Pop\Css\Color\Rgb', $rgb);
    }

    public function testParseHsl()
    {
        $hsl = Color::parse('hsla(240, 100%, 100%, 0.5)');
        $this->assertInstanceOf('Pop\Css\Color\Hsl', $hsl);
    }

    public function testParseHex()
    {
        $hex = Color::parse('#fff');
        $this->assertInstanceOf('Pop\Css\Color\Hex', $hex);
    }

    public function testParseException1()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $rgb = Color::parse('bad color');
    }

    public function testParseException2()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $rgb = Color::parse('rgba(255, 255, 255, 0.5');
    }

}