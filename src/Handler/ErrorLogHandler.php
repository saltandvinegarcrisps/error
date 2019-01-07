<?php declare(strict_types=1);

namespace Error\Handler;

use Throwable;

class ErrorLogHandler implements HandlerInterface
{
    use ExceptionMessageTrait;

    public function handle(Throwable $e): void
    {
        \error_log($this->getMessage($e));
    }
}
