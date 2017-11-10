<?php

namespace Pop\Css\Test;

use Pop\Css\Color;

class HslTest extends \PHPUnit_Framework_TestCase
{

    public function testHsl()
    {
        $hsl = new Color\Hsl(260, 100, 100, 1);
        $hsl->h = 240;
        $hsl->s = 60;
        $hsl->l = 40;
        $hsl->a = 0.5;
        $this->assertTrue(isset($hsl->h));
        $this->assertEquals(240, $hsl['h']);
        $this->assertEquals(60, $hsl['s']);
        $this->assertEquals(40, $hsl['l']);
        $this->assertEquals(0.5, $hsl['a']);
        $this->assertEquals(240, $hsl->h);
        $this->assertEquals(60, $hsl->s);
        $this->assertEquals(40, $hsl->l);
        $this->assertEquals(0.5, $hsl->a);
        $this->assertEquals('hsla(240, 60%, 40%, 0.5)', (string)$hsl);
        $this->assertTrue($hsl->hasA());
        $this->assertTrue($hsl->hasAlpha());
        $this->assertEquals(4, count($hsl->toArray(false)));
    }

    public function testRgbSetException()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $hsl = new Color\Hsl(260, 100, 100, 1);
        $hsl->q = 255;
    }

    public function testRgbGetException()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $hsl = new Color\Hsl(260, 100, 100, 1);
        $var = $hsl->q;
    }

    public function testRgbUnsetException()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $hsl = new Color\Hsl(260, 100, 100, 1);
        unset($hsl->h);
    }

    public function testRgbOffsetUnsetException()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $hsl = new Color\Hsl(260, 100, 100, 1);
        unset($hsl['h']);
    }

    public function testRgbSetRException()
    {
        $this->expectException('OutOfRangeException');
        $hsl = new Color\Hsl(460, 100, 100, 1);
    }

    public function testRgbSetGException()
    {
        $this->expectException('OutOfRangeException');
        $hsl = new Color\Hsl(260, 150, 100, 1);
    }

    public function testRgbSetBException()
    {
        $this->expectException('OutOfRangeException');
        $hsl = new Color\Hsl(260, 100, 150, 1);
    }

    public function testRgbSetAException()
    {
        $this->expectException('OutOfRangeException');
        $hsl = new Color\Hsl(260, 100, 100, 2);
    }

    public function testHslToRgb()
    {
        $hsl = new Color\Hsl(40, 75, 94, 0.5);
        //$rgb = new Color\Rgb(240, 180, 60, 0.5);
        $rgb = $hsl->toRgb();
        $this->assertEquals(240, $rgb['r']);
        $this->assertEquals(180, $rgb['g']);
        $this->assertEquals(60, $rgb['b']);
        $this->assertEquals(0.5, $rgb['a']);
        $this->assertEquals(240, $rgb->r);
        $this->assertEquals(180, $rgb->g);
        $this->assertEquals(60, $rgb->b);
        $this->assertEquals(0.5, $rgb->a);
        $this->assertEquals('rgba(240, 180, 60, 0.5)', (string)$rgb);
    }

    public function testHslToHex()
    {
        $hsl = new Color\Hsl(40, 75, 94, 0.5);
        $hex = $hsl->toHex();
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