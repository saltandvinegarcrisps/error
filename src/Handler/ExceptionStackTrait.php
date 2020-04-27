<?php declare(strict_types=1);

namespace Error\Handler;

use Error\Stack;
use Throwable;

trait ExceptionStackTrait
{
    protected function getStack(Throwable $e): Stack
    {
        return new Stack($e);
    }
}
