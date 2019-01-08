<?php declare(strict_types=1);

namespace Error\Handler;

use Error\Stacktrace;
use Throwable;

class JsonHandler implements HandlerInterface
{
    use ExceptionMessageTrait;

    public static function isJson(): bool
    {
        return \strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    public function handle(Throwable $e): void
    {
        $id = \sha1($this->getMessage($e));

        $stack = [[$e, new Stacktrace($e)]];

        while ($e = $e->getPrevious()) {
            $stack[] = [$e, new Stacktrace($e)];
        }

        \http_response_code(500);
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
