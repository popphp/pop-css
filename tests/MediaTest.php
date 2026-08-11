<?php

namespace Pop\Css\Test;

use Pop\Css;
use PHPUnit\Framework\TestCase;

class MediaTest extends TestCase
{

    public function testConstructor()
    {
        $media = new Css\Media('screen', ['max-width' => '480px'], 'only', 2);
        $this->assertInstanceOf('Pop\Css\Media', $media);
        $this->assertEquals('screen', $media->getType());
        $this->assertEquals('only', $media->getCondition());
        $this->assertEquals(2, $media->getTabSize());
        $this->assertTrue($media->hasFeature('max-width'));
        $this->assertEquals('480px', $media->getFeature('max-width'));
        $this->assertEquals(1, count($media->getFeatures()));
    }

    public function testRender()
    {
        $html = new Css\Selector('html');
        $html->setProperties([
            'margin'  => 0,
            'padding' => 0
        ]);
        $media = new Css\Media('screen', ['max-width' => '480px'], 'only', 2);
        $media->addSelector($html);

        $cssString = (string)$media;
        $this->assertStringContainsString('html {', $cssString);
    }

    public function testRenderPreservesInsertionOrder()
    {
        $media = new Css\Media('screen');
        $media->addSelectors([
            new Css\Selector('.bold'),
            new Css\Selector('html'),
            new Css\Selector('#login'),
        ]);

        $cssString = (string)$media;

        $boldPos  = strpos($cssString, '.bold');
        $htmlPos  = strpos($cssString, 'html');
        $loginPos = strpos($cssString, '#login');

        $this->assertLessThan($htmlPos, $boldPos);
        $this->assertLessThan($loginPos, $htmlPos);
    }

    public function testArrayAccess()
    {
        $media = new Css\Media('screen');
        $media['#login'] = new Css\Selector();

        $this->assertTrue(isset($media['#login']));
        $this->assertInstanceOf('Pop\Css\Selector', $media['#login']);

        unset($media['#login']);
        $this->assertFalse(isset($media['#login']));
    }

    public function testCount()
    {
        $media = new Css\Media('screen');
        $media->addSelectors([
            new Css\Selector('html'),
            new Css\Selector('#login')
        ]);
        $this->assertEquals(2, count($media));
    }

    public function testIterator()
    {
        $media = new Css\Media('screen');
        $media->addSelectors([
            new Css\Selector('html'),
            new Css\Selector('#login')
        ]);

        $i = 0;
        foreach ($media as $selector) {
            $i++;
        }
        $this->assertEquals(2, $i);
    }

    public function testHasFeatureReturnsFalseForMissingFeature()
    {
        $media = new Css\Media('screen', ['max-width' => '480px']);
        $this->assertFalse($media->hasFeature('min-width'));
    }

    public function testGetFeatureReturnsNullForMissingFeature()
    {
        $media = new Css\Media('screen', ['max-width' => '480px']);
        $this->assertNull($media->getFeature('min-width'));
    }

    public function testRenderWithNoSelectors()
    {
        $media = new Css\Media('screen', ['max-width' => '480px']);
        $cssString = (string)$media;

        $this->assertStringContainsString('@media screen and (max-width: 480px) {', $cssString);
        $this->assertStringEndsWith('}' . PHP_EOL, $cssString);
    }

    public function testRenderWithConditionAndNoFeaturesOmitsDanglingAnd()
    {
        $media = new Css\Media('screen', null, 'not');
        $media->addSelector(new Css\Selector('p'));

        $cssString = (string)$media;

        $this->assertStringStartsWith('@media not screen {', $cssString);
        $this->assertStringNotContainsString('and {', $cssString);
    }

    public function testRenderWithTypeOnlyAndNoFeaturesOmitsDanglingAnd()
    {
        $media = new Css\Media('print');
        $media->addSelector(new Css\Selector('p'));

        $cssString = (string)$media;

        $this->assertStringStartsWith('@media print {', $cssString);
        $this->assertStringNotContainsString('and {', $cssString);
    }

}