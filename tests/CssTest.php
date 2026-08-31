<?php

namespace Pop\Css\Test;

use Pop\Css;
use Pop\Color\Color;
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

    public function testAddSelectorMergesIntoExistingSameNameSelector()
    {
        // Regression test: addSelector() used to replace the existing
        // same-name selector wholesale, silently discarding any properties
        // it held that the new selector doesn't also set - real CSS cascade
        // semantics merge same-selector rules property-by-property instead.
        $css = new Css\Css();

        $original = new Css\Selector('h1');
        $original->setProperty('font-size', '32px');
        $original->setProperty('font-weight', 'bold');
        $css->addSelector($original);

        $colorOnly = new Css\Selector('h1');
        $colorOnly->setProperty('color', '#cc0000');
        $css->addSelector($colorOnly);

        $merged = $css->getSelector('h1');
        $this->assertEquals('32px', $merged->getProperty('font-size'));
        $this->assertEquals('bold', $merged->getProperty('font-weight'));
        $this->assertEquals('#cc0000', $merged->getProperty('color'));
    }

    public function testAddSelectorLaterPropertyOverridesEarlierOne()
    {
        $css = new Css\Css();
        $css->addSelector((new Css\Selector('.foo'))->setProperty('color', 'red'));
        $css->addSelector((new Css\Selector('.foo'))->setProperty('color', 'blue'));

        $this->assertEquals('blue', $css->getSelector('.foo')->getProperty('color'));
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

    public function testRemoveComment()
    {
        $css = new Css\Css();
        $css->addComment('First comment');
        $css->addComment('Second comment');
        $this->assertEquals(2, count($css->getComments()));

        $css->removeComment(0);

        $comments = $css->getComments();
        $this->assertEquals(1, count($comments));
        $this->assertTrue(isset($comments[0]));
        $this->assertEquals('Second comment', $comments[0]->getComment());
    }

    public function testRemoveAllComments()
    {
        $css = new Css\Css();
        $css->addComment('First comment');
        $css->addComment('Second comment');

        $css->removeAllComments();

        $this->assertEquals(0, count($css->getComments()));
        $this->assertFalse($css->hasComments());
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

    public function testRemoveMediaReindexesRemainingMedia()
    {
        $first  = new Css\Media('screen');
        $second = new Css\Media('print');
        $third  = new Css\Media('speech');

        $css = new Css\Css();
        $css->addMedia($first)
            ->addMedia($second)
            ->addMedia($third);

        $css->removeMedia(0);

        $remaining = $css->getAllMedia();
        $this->assertEquals(2, count($remaining));
        $this->assertTrue(isset($remaining[0]));
        $this->assertTrue(isset($remaining[1]));
        $this->assertSame($second, $remaining[0]);
        $this->assertSame($third, $remaining[1]);
    }

    public function testParseString()
    {
        $css = Css\Css::parseString(file_get_contents(__DIR__ . '/tmp/styles.css'));
        $this->assertTrue($css->hasSelector('html'));
    }

    public function testParseCssWithTwoRulesForSameSelectorMergesProperties()
    {
        // Regression test: two rules for the same selector within one
        // parseCss() call used to have the second wholesale-replace the
        // first, losing any property only the first rule set.
        $css = Css\Css::parseString('.foo { color: red; } .foo { font-size: 40px; }');

        $selector = $css->getSelector('.foo');
        $this->assertEquals('red', $selector->getProperty('color'));
        $this->assertEquals('40px', $selector->getProperty('font-size'));
    }

    public function testParseFile()
    {
        $css = Css\Css::parseFile(__DIR__ . '/tmp/styles.css');
        $this->assertTrue($css->hasSelector('html'));
    }

    public function testParseUri()
    {
        // file_get_contents() doesn't distinguish a URI scheme from a plain
        // local path, so a local file path exercises parseUri() without any
        // real network I/O (a live external URL is fragile by construction).
        $css = Css\Css::parseUri(__DIR__ . '/tmp/styles.css');
        $this->assertTrue($css->hasSelector('body'));
    }

    public function testParseUriThrowsOnFetchFailure()
    {
        // Regression test: parseCssUri() passed file_get_contents()'s result
        // straight into parseCss(string $cssString) - on a fetch failure
        // (404, unreachable host, etc.) that's `false`, which under
        // strict_types raised a raw TypeError instead of a clear exception.
        // A nonexistent local path fails file_get_contents() the same way a
        // dead URL does, so this exercises it with no network involved.
        $this->expectException('Pop\Css\Exception');
        Css\Css::parseUri(__DIR__ . '/tmp/does-not-exist.css');
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

    public function testRenderPreservesInsertionOrder()
    {
        $css = new Css\Css();
        $css->addSelectors([
            new Css\Selector('.bold'),
            new Css\Selector('html'),
            new Css\Selector('#login'),
        ]);

        $cssString = (string)$css;

        $boldPos  = strpos($cssString, '.bold');
        $htmlPos  = strpos($cssString, 'html');
        $loginPos = strpos($cssString, '#login');

        $this->assertLessThan($htmlPos, $boldPos);
        $this->assertLessThan($loginPos, $htmlPos);
    }

    public function testParsedSelectorColorPropertyIsReadableAsColorObject()
    {
        $css = Css\Css::parseString(".box {\n    color: #ff0000;\n    width: 50%;\n}\n");
        $selector = $css->getSelector('.box');

        $color = $selector->getColorProperty('color');
        $this->assertInstanceOf(Color\ColorInterface::class, $color);
        $this->assertEquals('#ff0000', $color->toCss());
        $this->assertNull($selector->getColorProperty('width'));
    }

    public function testParseSelectorsHandlesSemicolonAndColonInsideUrlValue()
    {
        $css      = Css\Css::parseString(".icon {\n    background: url(data:image/png;base64,iVBORw0KGgo=);\n    color: #fff;\n}\n");
        $selector = $css->getSelector('.icon');

        $this->assertEquals('url(data:image/png;base64,iVBORw0KGgo=)', $selector->getProperty('background'));
        $this->assertEquals('#fff', $selector->getProperty('color'));
    }

    public function testParseSelectorsHandlesUnmatchedClosingParenInValue()
    {
        $css      = Css\Css::parseString(".x {\n    content: \")\";\n    color: red;\n}\n");
        $selector = $css->getSelector('.x');

        $this->assertEquals('")"', $selector->getProperty('content'));
        $this->assertEquals('red', $selector->getProperty('color'));
    }

    public function testParseHandlesCommentPrecedingFinalMediaBlockWithNoTrailingContent()
    {
        $css = Css\Css::parseString(
            "p {\n    color: blue;\n}\n/**\n * comment\n */\n@media screen {\n    a {\n        color: red;\n    }\n}"
        );

        $media = $css->getMedia(0);
        $this->assertEquals('comment', $media->getComments()[0]->getComment());
        $this->assertTrue($media->hasSelector('a'));
    }

    public function testHasSelectorReturnsFalseForUnknownSelector()
    {
        $css = new Css\Css();
        $this->assertFalse($css->hasSelector('.does-not-exist'));
    }

    public function testGetSelectorReturnsNullForUnknownSelector()
    {
        $css = new Css\Css();
        $this->assertNull($css->getSelector('.does-not-exist'));
    }

    public function testCountWithNoSelectors()
    {
        $css = new Css\Css();
        $this->assertEquals(0, count($css));
    }

    public function testIteratorWithNoSelectors()
    {
        $css = new Css\Css();
        $i = 0;
        foreach ($css as $selector) {
            $i++;
        }
        $this->assertEquals(0, $i);
    }

    public function testParsedSelectorsRenderInSourceOrder()
    {
        $css       = Css\Css::parseFile(__DIR__ . '/tmp/styles.css');
        $cssString = (string)$css;

        $htmlPos = strpos($cssString, 'html {');
        $bodyPos = strpos($cssString, 'body {');
        $aPos    = strpos($cssString, 'a {');
        $loginPos = strpos($cssString, '.login {');

        $this->assertLessThan($bodyPos, $htmlPos);
        $this->assertLessThan($aPos, $bodyPos);
        $this->assertLessThan($loginPos, $aPos);
    }

    public function testParseMediaTypeAll()
    {
        $css = Css\Css::parseString("@media all {\n    p {\n        color: red;\n    }\n}\n");
        $this->assertEquals('all', $css->getMedia(0)->getType());
    }

    public function testParseMediaTypePrint()
    {
        $css = Css\Css::parseString("@media print {\n    p {\n        color: red;\n    }\n}\n");
        $this->assertEquals('print', $css->getMedia(0)->getType());
    }

    public function testParseMediaTypeSpeech()
    {
        $css = Css\Css::parseString("@media speech {\n    p {\n        color: red;\n    }\n}\n");
        $this->assertEquals('speech', $css->getMedia(0)->getType());
    }

    public function testParseMediaConditionNot()
    {
        $css   = Css\Css::parseString("@media not screen {\n    p {\n        color: red;\n    }\n}\n");
        $media = $css->getMedia(0);
        $this->assertEquals('not', $media->getCondition());
        $this->assertEquals('screen', $media->getType());
    }

    public function testParseMediaConditionOnly()
    {
        $css   = Css\Css::parseString("@media only screen {\n    p {\n        color: red;\n    }\n}\n");
        $media = $css->getMedia(0);
        $this->assertEquals('only', $media->getCondition());
        $this->assertEquals('screen', $media->getType());
    }

    public function testParseMediaWithNoPrecedingComment()
    {
        $css   = Css\Css::parseString("p {\n    color: blue;\n}\n@media screen {\n    a {\n        color: red;\n    }\n}\n");
        $media = $css->getMedia(0);
        $this->assertFalse($media->hasComments());
    }

    public function testMinifyPropagatesToMediaAndSelectors()
    {
        $html = new Css\Selector('html');
        $html->setProperty('margin', 0);

        $p = new Css\Selector('p');
        $p->setProperty('color', 'red');

        $media = new Css\Media('screen', ['max-width' => '480px']);
        $media->addSelector($p);

        $css = new Css\Css();
        $css->addSelector($html)
            ->addMedia($media);
        $css->minify(true);

        $expected = 'html{margin:0;} @media screen and (max-width: 480px) {p{color:red;}}';
        $this->assertEquals($expected, (string)$css);
    }

    public function testConstructorAcceptsCommentObject()
    {
        $comment = new Css\Comment('This is a comment');
        $css     = new Css\Css($comment);
        $this->assertTrue($css->hasComments());
    }

}
