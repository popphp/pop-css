<?php

namespace Pop\Css\Test;

use Pop\Css;

class CssTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $css = new Css\Css();
        $this->assertInstanceOf('Pop\Css\Css', $css);
    }

    public function testAddSelector()
    {
        $css = new Css\Css();
        $css->addSelectors([
            new Css\Selector('html'),
            new Css\Selector('#login'),
            new Css\Selector('.login-div')
        ]);

        $this->assertTrue($css->hasSelector('html'));
        $this->assertTrue($css->hasSelector('#login'));
        $this->assertTrue($css->hasSelector('.login-div'));

        $this->assertInstanceOf('Pop\Css\Selector', $css->getSelector('html'));
        $this->assertInstanceOf('Pop\Css\Selector', $css->getSelector('#login'));
        $this->assertInstanceOf('Pop\Css\Selector', $css->getSelector('.login-div'));
    }

    public function testGetSelector()
    {
        $css = new Css\Css();
        $css->addSelectors([
            new Css\Selector('html'),
            new Css\Selector('#login'),
            new Css\Selector('.login-div')
        ]);

        $this->assertInstanceOf('Pop\Css\Selector', $css->getSelector('html'));
        $this->assertInstanceOf('Pop\Css\Selector', $css->getSelector('#login'));
        $this->assertInstanceOf('Pop\Css\Selector', $css->getSelector('.login-div'));
    }

    public function testRemoveSelector()
    {
        $css = new Css\Css();
        $css->addSelectors([
            new Css\Selector('html'),
            new Css\Selector('#login'),
            new Css\Selector('.login-div')
        ]);

        $this->assertTrue($css->hasSelector('html'));
        $this->assertTrue($css->hasSelector('#login'));
        $this->assertTrue($css->hasSelector('.login-div'));

        $css->removeSelector('html');
        $css->removeSelector('#login');
        $css->removeSelector('.login-div');

        $this->assertFalse($css->hasSelector('html'));
        $this->assertFalse($css->hasSelector('#login'));
        $this->assertFalse($css->hasSelector('.login-div'));
    }

    public function testAddComment()
    {
        $comment = new Css\Comment('Test comment');
        $css = new Css\Css();
        $css->addComment($comment);
        $this->assertEquals(1, count($css->getComments()));
        $this->assertEquals('Test comment', $comment->getComment());
    }

    public function testMinify()
    {
        $css = new Css\Css();
        $css->minify();
        $this->assertTrue($css->isMinified());
    }

    public function testAddMedia()
    {
        $css = new Css\Css();
        $css->addMedia(new Css\Media());
        $this->assertEquals(1, count($css->getAllMedia()));
        $this->assertInstanceOf('Pop\Css\Media', $css->getMedia(0));
        $css->removeMedia(0);
        $css->removeAllMedia();
        $this->assertEquals(0, count($css->getAllMedia()));
    }

    public function testParseString()
    {
        $css = Css\Css::parseString(file_get_contents(__DIR__ . '/tmp/styles.css'));
        $this->assertTrue($css->hasSelector('html'));
    }

    public function testParseFile()
    {
        $css = Css\Css::parseFile(__DIR__ . '/tmp/styles.css');
        $this->assertTrue($css->hasSelector('html'));
    }

    public function testParseFileException()
    {
        $this->expectException('Pop\Css\Exception');
        $css = Css\Css::parseFile(__DIR__ . '/tmp/bad.css');
    }

    public function testRender()
    {
        $id = new Css\Selector('#id');
        $id->setProperties([
            'margin'  => 0,
            'padding' => 0
        ]);
        $css = Css\Css::parseFile(__DIR__ . '/tmp/styles.css');
        $css->addSelector($id);
        $cssString = (string)$css;
        $this->assertContains('html {', $cssString);
    }

    public function testOffsetMethods()
    {
        $css = new Css\Css();
        $css->addSelectors([
            new Css\Selector('html'),
            new Css\Selector('#login')
        ]);

        $css['.login-div'] = new Css\Selector();
        $this->assertTrue(isset($css['#login']));
        $this->assertTrue(isset($css['.login-div']));
        $this->assertInstanceOf('Pop\Css\Selector', $css['#login']);
        unset($css['#login']);
        $this->assertFalse(isset($css['#login']));
    }

    public function testOffsetSetException()
    {
        $this->expectException('Pop\Css\Exception');
        $css = new Css\Css();
        $css['.login-div'] = [123];
    }

}