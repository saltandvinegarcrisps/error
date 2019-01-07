<?php declare(strict_types=1);

namespace Error\Handler;

use Throwable;

trait ExceptionMessageTrait
{
    protected function getMessage(Throwable $e): string
    {
        return \sprintf(
            'Uncaught %s: %s in %s on %s',
            \get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
    }
}
