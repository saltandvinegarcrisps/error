<?php declare(strict_types=1);

namespace Error\Handler;

use Throwable;

class JsonHandler implements HandlerInterface
{
    use ExceptionMessageTrait, ExceptionStackTrait;

    public function handle(Throwable $e): void
    {
        $id = \sha1($this->getMessage($e));

        $stack = $this->getStack($e);

        \header('Content-Type: application/json', true, 500);
        echo \json_encode([
            'id' => $id,
            'links' => [
                'self' => $_SERVER['REQUEST_URI'] ?? '/',
            ],
            'status' => 500,
            'code' => $stack[0][0]->getCode(),
            'title' => $this->getMessage($stack[0][0]),
            'detail' => $stack,
        ], JSON_PRETTY_PRINT);
    }
}
