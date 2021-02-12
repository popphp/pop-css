<?php

namespace Pop\Css\Test;

use Pop\Css;
use PHPUnit\Framework\TestCase;

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

}