<?php declare(strict_types=1);

namespace Error\Handler;

use Error\Stacktrace;
use Throwable;

class WebHandler implements HandlerInterface
{
    use ExceptionMessageTrait;

    public static function isWeb(): bool
    {
        return \strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'text/html') !== false;
    }

    public function handle(Throwable $e): void
    {
        \http_response_code(500);
        \header('Content-Type: text/html;charset=utf8');

        $stack = [[$e, new Stacktrace($e)]];

        while ($e = $e->getPrevious()) {
            $stack[] = [$e, new Stacktrace($e)];
        }

        require __DIR__ . '/../Resources/debug.html';
    }
}
