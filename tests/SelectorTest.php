<?php

namespace Pop\Css\Test;

use Pop\Css;

class SelectorTest extends \PHPUnit_Framework_TestCase
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

    public function testRender()
    {
        $selector = new Css\Selector('div');
        $selector->minify();
        $selector->margin = 0;
        $selector['padding'] = 0;
        $css = (string)$selector;
        $this->assertContains('div{', $css);
    }

}