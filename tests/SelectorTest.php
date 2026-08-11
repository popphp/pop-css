<?php

namespace Pop\Css\Test;

use Pop\Css;
use PHPUnit\Framework\TestCase;
use Pop\Color\Color;

class SelectorTest extends TestCase
{

    public function testGetTabSize()
    {
        $selector = new Css\Selector('div', 2);
        $this->assertEquals(2, $selector->getTabSize());
    }

    public function testIsMultiple()
    {
        $selector = new Css\Selector('div, p');
        $this->assertTrue($selector->isMultipleSelector());
    }

    public function testHasDescendant()
    {
        $selector = new Css\Selector('div > p');
        $this->assertTrue($selector->hasDescendant());
    }

    public function testProperties()
    {
        $selector = new Css\Selector('div');
        $selector->margin = 0;
        $selector['padding'] = 0;
        $selector['width'] = '150px;';
        $selector->removeProperty('width');
        $this->assertTrue($selector->hasProperty('padding'));
        $this->assertFalse($selector->hasProperty('width'));
        $this->assertEquals(0, $selector->getProperty('padding'));
        $this->assertEquals(0, $selector->margin);
        $this->assertEquals(0, $selector['padding']);
        $this->assertEquals(2, count($selector->getProperties()));
        $this->assertEquals(2, $selector->count());

        $i = 0;
        foreach ($selector as $s) {
            $i++;
        }

        $this->assertEquals(2, $i);
        $this->assertFalse($selector->isMinified());
        if (isset($selector->margin)) {
            unset($selector->margin);
        }
        if (isset($selector['padding'])) {
            unset($selector['padding']);
        }

        $this->assertEquals(0, $selector->count());
        $this->assertEquals(0, count($selector->getComments()));
    }

    public function testMarginProperty()
    {
        $html1 = new Css\Selector('html');
        $html1->setProperty('margin', '10px 5px 15px 20px');
        $html2 = new Css\Selector('html');
        $html2->setProperty('margin', '10px 5px 15px');
        $html3 = new Css\Selector('html');
        $html3->setProperty('margin', '10px 5px');
        $html4 = new Css\Selector('html');
        $html4->setProperty('margin', '10px');

        $this->assertEquals('10px', $html1['margin-top']);
        $this->assertEquals('5px', $html1['margin-right']);
        $this->assertEquals('15px', $html1['margin-bottom']);
        $this->assertEquals('20px', $html1['margin-left']);

        $this->assertEquals('10px', $html2['margin-top']);
        $this->assertEquals('5px', $html2['margin-right']);
        $this->assertEquals('15px', $html2['margin-bottom']);
        $this->assertEquals('5px', $html2['margin-left']);

        $this->assertEquals('10px', $html3['margin-top']);
        $this->assertEquals('5px', $html3['margin-right']);
        $this->assertEquals('10px', $html3['margin-bottom']);
        $this->assertEquals('5px', $html3['margin-left']);

        $this->assertEquals('10px', $html4['margin-top']);
        $this->assertEquals('10px', $html4['margin-right']);
        $this->assertEquals('10px', $html4['margin-bottom']);
        $this->assertEquals('10px', $html4['margin-left']);
    }

    public function testPaddingProperty()
    {
        $html1 = new Css\Selector('html');
        $html1->setProperty('padding', '10px 5px 15px 20px');
        $html2 = new Css\Selector('html');
        $html2->setProperty('padding', '10px 5px 15px');
        $html3 = new Css\Selector('html');
        $html3->setProperty('padding', '10px 5px');
        $html4 = new Css\Selector('html');
        $html4->setProperty('padding', '10px');

        $this->assertEquals('10px', $html1['padding-top']);
        $this->assertEquals('5px', $html1['padding-right']);
        $this->assertEquals('15px', $html1['padding-bottom']);
        $this->assertEquals('20px', $html1['padding-left']);

        $this->assertEquals('10px', $html2['padding-top']);
        $this->assertEquals('5px', $html2['padding-right']);
        $this->assertEquals('15px', $html2['padding-bottom']);
        $this->assertEquals('5px', $html2['padding-left']);

        $this->assertEquals('10px', $html3['padding-top']);
        $this->assertEquals('5px', $html3['padding-right']);
        $this->assertEquals('10px', $html3['padding-bottom']);
        $this->assertEquals('5px', $html3['padding-left']);

        $this->assertEquals('10px', $html4['padding-top']);
        $this->assertEquals('10px', $html4['padding-right']);
        $this->assertEquals('10px', $html4['padding-bottom']);
        $this->assertEquals('10px', $html4['padding-left']);
    }

    public function testRender()
    {
        $selector = new Css\Selector('div');
        $selector->minify();
        $selector->margin = 0;
        $selector['padding'] = 0;
        $css = (string)$selector;
        $this->assertStringContainsString('div{', $css);
    }

    public function testSetPropertyAcceptsColorInterface()
    {
        $selector = new Css\Selector('.box');
        $selector->setProperty('color', Color::rgb(255, 0, 0));
        $this->assertEquals('rgb(255, 0, 0)', $selector->getProperty('color'));
    }

    public function testSetPropertyNormalizesCmykToValidCss()
    {
        $selector = new Css\Selector('.box');
        $selector->setProperty('color', Color::cmyk(30, 20, 10, 5));
        $this->assertEquals('rgb(170, 194, 218)', $selector->getProperty('color'));
        $this->assertStringContainsString('rgb(170, 194, 218)', (string)$selector);
    }

    public function testSetPropertyNormalizesGrayscaleToValidCss()
    {
        $selector = new Css\Selector('.box');
        $selector->setProperty('color', Color::grayscale(50));
        $this->assertEquals('rgb(128, 128, 128)', $selector->getProperty('color'));
    }

    public function testSetPropertyAcceptsNewerColorSpace()
    {
        $selector = new Css\Selector('.box');
        $selector->setProperty('color', Color::oklch(0.7, 0.15, 30));
        $this->assertEquals('oklch(0.7 0.15 30)', $selector->getProperty('color'));
    }

    public function testMagicSetAcceptsColorInterface()
    {
        $selector = new Css\Selector('.box');
        $selector->color = Color::hex('#ff0000');
        $this->assertEquals('#ff0000', $selector->color);
    }

    public function testArrayAccessSetAcceptsColorInterface()
    {
        $selector = new Css\Selector('.box');
        $selector['border-color'] = Color::rgb(0, 0, 0);
        $this->assertEquals('rgb(0, 0, 0)', $selector['border-color']);
    }

    public function testGetColorPropertyReturnsColorObjectForColorSetViaColorInterface()
    {
        $selector = new Css\Selector('.box');
        $selector->setProperty('color', Color::rgb(255, 0, 0));
        $color = $selector->getColorProperty('color');
        $this->assertInstanceOf(Color\ColorInterface::class, $color);
        $this->assertEquals('rgb(255, 0, 0)', $color->toCss());
    }

    public function testGetColorPropertyReturnsColorObjectForPlainColorStringLiteral()
    {
        $selector = new Css\Selector('.box');
        $selector->setProperty('color', '#ff0000');
        $color = $selector->getColorProperty('color');
        $this->assertInstanceOf(Color\ColorInterface::class, $color);
        $this->assertEquals('#ff0000', $color->toCss());
    }

    public function testGetColorPropertyReturnsNullForNonColorProperty()
    {
        $selector = new Css\Selector('.box');
        $selector->setProperty('width', '50%');
        $this->assertNull($selector->getColorProperty('width'));
    }

    public function testGetColorPropertyReturnsNullForMissingProperty()
    {
        $selector = new Css\Selector('.box');
        $this->assertNull($selector->getColorProperty('color'));
    }

    public function testGetColorPropertyReturnsNullForMalformedPrefixedColorString()
    {
        $selector = new Css\Selector('.box');
        $selector->setProperty('content', 'rgb');
        $this->assertNull($selector->getColorProperty('content'));
    }

    public function testGetColorPropertyMisidentifiesFourValueShorthandAsCmyk()
    {
        $selector = new Css\Selector('.box');
        $selector->setProperty('margin', '1px 2px 3px 4px');
        $color = $selector->getColorProperty('margin');
        $this->assertInstanceOf(Color\ColorInterface::class, $color);
        $this->assertEquals('rgb(0, 240, 237)', $color->toCss());
    }

    public function testIssetReturnsTrueForSynthesizedMarginShorthand()
    {
        $selector = new Css\Selector('html');
        $selector->setProperty('margin', '10px 5px');
        $this->assertTrue(isset($selector['margin-top']));
        $this->assertTrue(isset($selector['margin-right']));
        $this->assertTrue(isset($selector['margin-bottom']));
        $this->assertTrue(isset($selector['margin-left']));
    }

    public function testIssetReturnsTrueForSynthesizedPaddingShorthand()
    {
        $selector = new Css\Selector('html');
        $selector->setProperty('padding', '10px 5px');
        $this->assertTrue(isset($selector['padding-top']));
    }

    public function testIssetReturnsFalseForInvalidShorthandSuffix()
    {
        $selector = new Css\Selector('html');
        $selector->setProperty('margin', '10px 5px');
        $this->assertFalse(isset($selector['margin-foo']));
    }

    public function testIssetReturnsFalseWhenShorthandNotSet()
    {
        $selector = new Css\Selector('html');
        $this->assertFalse(isset($selector['margin-top']));
    }

    public function testIsElementSelector()
    {
        $selector = new Css\Selector('div');
        $this->assertTrue($selector->isElementSelector());
        $this->assertFalse($selector->isIdSelector());
        $this->assertFalse($selector->isClassSelector());
    }

    public function testIsIdSelector()
    {
        $selector = new Css\Selector('#login');
        $this->assertTrue($selector->isIdSelector());
        $this->assertFalse($selector->isElementSelector());
    }

    public function testIsClassSelector()
    {
        $selector = new Css\Selector('.bold');
        $this->assertTrue($selector->isClassSelector());
        $this->assertFalse($selector->isElementSelector());
    }

    public function testIsNotMultipleSelector()
    {
        $selector = new Css\Selector('div');
        $this->assertFalse($selector->isMultipleSelector());
    }

    public function testHasNoDescendant()
    {
        $selector = new Css\Selector('div');
        $this->assertFalse($selector->hasDescendant());
    }

    public function testGetName()
    {
        $selector = new Css\Selector('.bold');
        $this->assertEquals('.bold', $selector->getName());
    }

    public function testAddCommentRendersAboveRule()
    {
        $selector = new Css\Selector('p');
        $selector->setProperty('color', 'red');
        $selector->addComment('This is a comment for the P selector');

        $rendered = (string)$selector;

        $this->assertStringContainsString('This is a comment for the P selector', $rendered);
        $this->assertLessThan(strpos($rendered, 'p {'), strpos($rendered, 'This is a comment'));
    }

}