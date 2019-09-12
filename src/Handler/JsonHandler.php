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
        [$exception, $trace] = reset($stack);

        if (!\headers_sent()) {
            \header('Content-Type: application/json', true, 500);
        }
        echo \json_encode([
            'id' => $id,
            'links' => [
                'self' => $_SERVER['REQUEST_URI'] ?? '/',
            ],
            'status' => 500,
            'code' => $exception->getCode(),
            'title' => \sprintf('Uncaught %s', get_class($exception)),
            'detail' => $exception->getMessage(),
            'source' => \array_map(function ($frame) {
                return $frame->getFile().':'.$frame->getLine();
            }, \array_filter($trace->getFrames(), function ($frame) {
                return $frame->hasFile() && $frame->hasLine();
            })),
        ], JSON_PRETTY_PRINT);
    }
}
