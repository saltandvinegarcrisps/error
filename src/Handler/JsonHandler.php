<?php declare(strict_types=1);

namespace Error\Handler;

use Error\Stacktrace;
use Throwable;

class JsonHandler implements HandlerInterface
{
    use ExceptionMessageTrait;

    public static function isJson(): bool
    {
        return \strpos($_SERVER['HTTP_CONTENT_TYPE'] ?? '', 'application/json') !== false;
    }

    public function handle(Throwable $e): void
    {
        $id = \sha1($this->getMessage($e));
        $trace = new Stacktrace($e);

        \http_response_code(500);
        echo \json_encode([
            'id' => $id,
            'links' => [
                'self' => $_SERVER['REQUEST_URI'] ?? '/',
            ],
            'status' => 500,
            'code' => $e->getCode(),
            'title' => $this->getMessage($e),
            'detail' => $trace->getFrames(),
        ], JSON_PRETTY_PRINT);
    }
}
