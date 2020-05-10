<?php declare(strict_types=1);

namespace Error\Handler;

use Error\Traits;
use Throwable;

class EchoHandler implements HandlerInterface
{
    use Traits\ExceptionMessage;

    public function handle(Throwable $e): void
    {
        echo $e;
    }
}
