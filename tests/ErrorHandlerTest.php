<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class ErrorHandlerTest extends TestCase
{
    public function testErrors(): void
    {
        $error = new \Error\ErrorHandler();
        $error->attach(new class($this) implements \Error\Handler\HandlerInterface {
            protected $test;
            public function __construct($test) {
                $this->test = $test;
            }
            public function handle(\Throwable $exception): void {
                $this->test->assertEquals('yo', $exception->getMessage());
            }
        });
        $error->onException(new \Exception('yo'));
    }
}
