<?php declare(strict_types=1);

namespace Error\Handler;

use Throwable;

class ConsoleHandler implements HandlerInterface
{
    use ExceptionMessageTrait;

    public static function isConsole(): bool
    {
        return \php_sapi_name() === 'cli';
    }

    public function handle(Throwable $e): void
    {
        $handle = \fopen('php://stderr', 'a');
        if (!\is_resource($handle)) {
            throw new \ErrorException('Failed to open php://stderr stream');
        }
        \fwrite($handle, $this->getMessage($e).PHP_EOL);
        \fclose($handle);
    }
}
