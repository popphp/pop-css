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

}