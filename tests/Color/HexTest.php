<?php

namespace Pop\Css\Test;

use Pop\Css\Color;
use PHPUnit\Framework\TestCase;

class HexTest extends TestCase
{

    public function testHex()
    {
        $hex = new Color\Hex('#fff');
        $hex->r = 'f0';
        $hex->g = 'b4';
        $hex->b = '3c';
        $hex->hex = '#f0b43c';
        $this->assertTrue(isset($hex->r));
        $this->assertEquals('f0b43c', $hex['hex']);
        $this->assertEquals('f0', $hex['r']);
        $this->assertEquals('b4', $hex['g']);
        $this->assertEquals('3c', $hex['b']);
        $this->assertEquals('f0b43c', $hex->hex);
        $this->assertEquals('f0', $hex->r);
        $this->assertEquals('b4', $hex->g);
        $this->assertEquals('3c', $hex->b);
        $this->assertEquals('#f0b43c', (string)$hex);
        $this->assertEquals(4, count($hex->toArray()));
        $this->assertEquals(4, count($hex->toArray(false)));
    }

    public function testHexSetException()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $hex = new Color\Hex('#f0b43c');
        $hex->q = 255;
    }

    public function testHexGetException()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $hex = new Color\Hex('#f0b43c');
        $var = $hex->q;
    }

    public function testHexUnsetException()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $hex = new Color\Hex('#f0b43c');
        unset($hex->r);
    }

    public function testHexOffsetUnsetException()
    {
        $this->expectException('Pop\Css\Color\Exception');
        $hex = new Color\Hex('#f0b43c');
        unset($hex['r']);
    }

    public function testHexSetRException()
    {
        $this->expectException('OutOfRangeException');
        $hex = new Color\Hex('#f0b43c');
        $hex->setR('gg');
    }

    public function testHexSetGException()
    {
        $this->expectException('OutOfRangeException');
        $hex = new Color\Hex('#f0b43c');
        $hex->setB('gg');
    }

    public function testHexSetBException()
    {
        $this->expectException('OutOfRangeException');
        $hex = new Color\Hex('#f0b43c');
        $hex->setG('gg');
    }

    public function testHexSetHexOutOfRangeException1()
    {
        $this->expectException('OutOfRangeException');
        $hex = new Color\Hex('33');
    }

    public function testHexSetHexOutOfRangeException2()
    {
        $this->expectException('OutOfRangeException');
        $hex = new Color\Hex('gggggg');
    }

    public function testHexToHsl()
    {
        $hex = new Color\Hex('#f0b43c');
        $hsl = $hex->toHsl();
        $this->assertEquals(40, $hsl['h']);
        $this->assertEquals(75, $hsl['s']);
        $this->assertEquals(94, $hsl['l']);
        $this->assertEquals(40, $hsl->h);
        $this->assertEquals(75, $hsl->s);
        $this->assertEquals(94, $hsl->l);
        $this->assertEquals('hsl(40, 75%, 94%)', (string)$hsl);
    }

    public function testHexToRgb()
    {
        $hex = new Color\Hex('#fff');
        $rgb = $hex->toRgb();
        $this->assertEquals(255, $rgb->r);
        $this->assertEquals(255, $rgb->g);
        $this->assertEquals(255, $rgb->b);
        $this->assertEquals(255, $rgb['r']);
        $this->assertEquals(255, $rgb['g']);
        $this->assertEquals(255, $rgb['b']);
        $this->assertEquals('rgb(255, 255, 255)', (string)$rgb);
    }

}