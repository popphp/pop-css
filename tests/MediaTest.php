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
        $this->assertContains('html {', $cssString);
    }

}