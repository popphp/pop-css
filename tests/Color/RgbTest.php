<?php

namespace Pop\Css\Test;

use Pop\Css\Color;
use PHPUnit\Framework\TestCase;

class RgbTest extends TestCase
{

    public function testRgb()
    {
        $rgb = new Color\Rgb(240, 240, 240, 1);
        $rgb->r = 255;
        $rgb->g = 255;
        $rgb->b = 255;
        $rgb->a = 0.5;
        $this->assertTrue(isset($rgb->r));
        $this->assertEquals(255, $rgb['r']);
        $this->assertEquals(255, $rgb['g']);
        $this->assertEquals(255, $rgb['b']);
        $this->assertEquals(0.5, $rgb['a']);
        $this->assertEquals(255, $rgb->r);
        $this->assertEquals(255, $rgb->g);
        $this->assertEquals(255, $rgb->b);
        $this->assertEquals(0.5, $rgb->a);
        $this->assertEquals('rgba(255, 255, 255, 0.5)', (string)$rgb);
        $this->assertTrue($rgb->hasA());
        $this->assertTrue($rgb->hasAlpha());
        $this->assertEquals(4, count($rgb->toArray(false)));
    }

    public function testRgbSetException()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $rgb = new Color\Rgb(255, 255, 255, 0.5);
        $rgb->q = 255;
    }

    public function testRgbGetException()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $rgb = new Color\Rgb(255, 255, 255, 0.5);
        $var = $rgb->q;
    }

    public function testRgbUnsetException()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $rgb = new Color\Rgb(255, 255, 255, 0.5);
        unset($rgb->r);
    }

    public function testRgbOffsetUnsetException()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $rgb = new Color\Rgb(255, 255, 255, 0.5);
        unset($rgb['r']);
    }

    public function testRgbSetRException()
    {
        $this->expectException('OutOfRangeException');
        $rgb = new Color\Rgb(300, 255, 255, 0.5);
    }

    public function testRgbSetGException()
    {
        $this->expectException('OutOfRangeException');
        $rgb = new Color\Rgb(255, 300, 255, 0.5);
    }

    public function testRgbSetBException()
    {
        $this->expectException('OutOfRangeException');
        $rgb = new Color\Rgb(255, 255, 300, 0.5);
    }

    public function testRgbSetAException()
    {
        $this->expectException('OutOfRangeException');
        $rgb = new Color\Rgb(255, 255, 255, 20);
    }

    public function testRgbToHsl()
    {
        $rgb = new Color\Rgb(240, 180, 60, 0.5);
        $hsl = $rgb->toHsl();
        $this->assertEquals(40, $hsl['h']);
        $this->assertEquals(75, $hsl['s']);
        $this->assertEquals(94, $hsl['l']);
        $this->assertEquals(0.5, $hsl['a']);
        $this->assertEquals(40, $hsl->h);
        $this->assertEquals(75, $hsl->s);
        $this->assertEquals(94, $hsl->l);
        $this->assertEquals(0.5, $hsl->a);
        $this->assertEquals('hsla(40, 75%, 94%, 0.5)', (string)$hsl);
    }

    public function testRgbToHex()
    {
        $rgb = new Color\Rgb(240, 180, 60, 0.5);
        $hex = $rgb->toHex();
        $this->assertEquals('f0b43c', $hex['hex']);
        $this->assertEquals('f0', $hex['r']);
        $this->assertEquals('b4', $hex['g']);
        $this->assertEquals('3c', $hex['b']);
        $this->assertEquals('f0b43c', $hex->hex);
        $this->assertEquals('f0', $hex->r);
        $this->assertEquals('b4', $hex->g);
        $this->assertEquals('3c', $hex->b);
    }

}