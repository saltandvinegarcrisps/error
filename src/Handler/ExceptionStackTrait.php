<?php declare(strict_types=1);

namespace Error\Handler;

use Error\Stacktrace;
use Throwable;

trait ExceptionStackTrait
{
    protected function getStack(Throwable $e): array
    {
        $stack = [[$e, new Stacktrace($e)]];

        while ($e = $e->getPrevious()) {
            $stack[] = [$e, new Stacktrace($e)];
        }

        return $stack;
    }
}
