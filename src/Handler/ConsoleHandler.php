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
        echo $this->getMessage($e).PHP_EOL;
    }
}
