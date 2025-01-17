<?php

namespace Pop\Css\Test;

use Pop\Css;
use PHPUnit\Framework\TestCase;

class CssTest extends TestCase
{

    public function testConstructor()
    {
        $html     = new Css\Selector('html');
        $comment  = 'This is a comment';
        $media    = new Css\Media();
        $elements = [
            new Css\Selector('#login'),
            new Css\Selector('.login-div'),
            new Css\Media(),
            'This is a comment'
        ];
        $css = new Css\Css($html, $comment, $media, $elements);
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
        $comment = new Css\Comment('Test comment', 80, true);
        $css = new Css\Css();
        $css->addComment($comment);
        $this->assertTrue($css->hasComments());
        $this->assertEquals(1, count($css->getComments()));
        $this->assertEquals('Test comment', $comment->getComment());
        $this->assertTrue(str_contains($comment->render(), 'Test comment'));
        $this->assertTrue($comment->hasTrailingNewLine());
        $this->assertEquals(80, $comment->getWrap());
    }

    public function testAddSingleLineComment()
    {
        $comment = new Css\Comment('Test comment', 0, false);
        $css = new Css\Css();
        $css->addComment($comment);
        $this->assertTrue($css->hasComments());
        $this->assertEquals(1, count($css->getComments()));
        $this->assertEquals('/* Test comment */', (string)$comment);
        $this->assertFalse($comment->hasTrailingNewLine());
        $this->assertEquals(0, $comment->getWrap());
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

    public function testParseUri()
    {
        $css = Css\Css::parseUri('https://www.popphp.org/assets/app.css');
        $this->assertTrue($css->hasSelector('body'));
    }

    public function testParseFileException()
    {
        $this->expectException('Pop\Css\Exception');
        $css = Css\Css::parseFile(__DIR__ . '/tmp/bad.css');
    }

    public function testCount()
    {
        $css = new Css\Css();
        $css->addSelectors([
            new Css\Selector('html'),
            new Css\Selector('#login'),
            new Css\Selector('.login-div')
        ]);
        $this->assertEquals(3, count($css));
    }

    public function testIterator()
    {

        $css = new Css\Css();
        $css->addSelectors([
            new Css\Selector('html'),
            new Css\Selector('#login'),
            new Css\Selector('.login-div')
        ]);
        $i = 0;
        foreach ($css as $selector) {
            $i++;
        }
        $this->assertEquals(3, $i);
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
        $this->assertStringContainsString('html {', $cssString);
    }

    public function testWriteToFile()
    {
        $id = new Css\Selector('#id');
        $id->setProperties([
            'margin'  => 0,
            'padding' => 0
        ]);
        $this->assertFileDoesNotExist(__DIR__ . '/tmp/test.css');
        $css = Css\Css::parseFile(__DIR__ . '/tmp/styles.css');
        $css->addSelector($id);
        $css->writeToFile(__DIR__ . '/tmp/test.css');
        $this->assertFileExists(__DIR__ . '/tmp/test.css');
        unlink(__DIR__ . '/tmp/test.css');
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
