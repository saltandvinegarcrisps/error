<?php declare(strict_types=1);

namespace Error\Handler;

use Error\Traits;
use Throwable;

class ErrorLogHandler implements HandlerInterface
{
    use Traits\ExceptionMessage;

    public function handle(Throwable $e): void
    {
        \error_log($this->getMessageWithSource($e));
    }
}
