<?php

namespace Pop\Css\Test;

use Pop\Css\Exception;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{

    public function testIsThrowable()
    {
        $this->expectException(Exception::class);
        throw new Exception('Test exception message');
    }

    public function testMessageRoundTrips()
    {
        $exception = new Exception('Test exception message');
        $this->assertEquals('Test exception message', $exception->getMessage());
    }

    public function testIsInstanceOfBaseException()
    {
        $exception = new Exception('Test exception message');
        $this->assertInstanceOf(\Exception::class, $exception);
    }

}
