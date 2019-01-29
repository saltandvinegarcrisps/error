<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class ErrorHandlerTest extends TestCase
{
    public function testAttach(): void
    {
        $listeners = new \SplObjectStorage;
        $error = new \Error\ErrorHandler($listeners);
        $handler = $this->createMock(\Error\Handler\HandlerInterface::class);
        $error->attach($handler);
        $this->assertTrue($listeners->count() === 1);
    }
}
