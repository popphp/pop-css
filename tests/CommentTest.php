<?php

namespace Pop\Css\Test;

use Pop\Css\Comment;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{

    public function testConstructorDefaults()
    {
        $comment = new Comment('Test comment');
        $this->assertEquals('Test comment', $comment->getComment());
        $this->assertEquals(80, $comment->getWrap());
        $this->assertFalse($comment->hasTrailingNewLine());
    }

    public function testSetComment()
    {
        $comment = new Comment('Original');
        $comment->setComment('Updated');
        $this->assertEquals('Updated', $comment->getComment());
    }

    public function testSetWrap()
    {
        $comment = new Comment('Test comment');
        $comment->setWrap(40);
        $this->assertEquals(40, $comment->getWrap());
    }

    public function testSetTrailingNewLine()
    {
        $comment = new Comment('Test comment');
        $comment->setTrailingNewLine(true);
        $this->assertTrue($comment->hasTrailingNewLine());
    }

    public function testRenderSingleLine()
    {
        $comment = new Comment('Test comment', 0, false);
        $this->assertEquals('/* Test comment */', $comment->render());
    }

    public function testRenderSingleLineWithTrailingNewLine()
    {
        $comment = new Comment('Test comment', 0, true);
        $this->assertEquals('/* Test comment */' . PHP_EOL, $comment->render());
    }

    public function testRenderWrapped()
    {
        $comment  = new Comment('Test comment', 80, false);
        $rendered = $comment->render();
        $this->assertStringStartsWith('/*' . PHP_EOL, $rendered);
        $this->assertStringContainsString(' * Test comment' . PHP_EOL, $rendered);
        $this->assertStringEndsWith(' */', $rendered);
    }

    public function testRenderPreservesEmbeddedNewLinesWithoutRewrapping()
    {
        $comment  = new Comment('Line one' . PHP_EOL . 'Line two', 80, false);
        $rendered = $comment->render();
        $this->assertStringContainsString(' * Line one' . PHP_EOL, $rendered);
        $this->assertStringContainsString(' * Line two' . PHP_EOL, $rendered);
    }

    public function testToString()
    {
        $comment = new Comment('Test comment', 0, false);
        $this->assertEquals('/* Test comment */', (string)$comment);
    }

    public function testRenderWrapsLongSingleLineComment()
    {
        $long    = 'This comment is intentionally long enough that it must exceed the default eighty character wrap width for testing.';
        $comment = new Comment($long, 80, false);

        $expected = '/*' . PHP_EOL
            . ' * This comment is intentionally long enough that it must exceed the default eighty' . PHP_EOL
            . ' * character wrap width for testing.' . PHP_EOL
            . ' */';

        $this->assertEquals($expected, $comment->render());
    }

}
