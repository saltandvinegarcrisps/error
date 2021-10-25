<?php

declare(strict_types=1);

namespace Error\Traits;

use Error\Stack;
use Throwable;

trait ExceptionStack
{
    protected function getStack(Throwable $e): Stack
    {
        return new Stack($e);
    }
}
