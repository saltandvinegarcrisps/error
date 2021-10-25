<?php

declare(strict_types=1);

namespace Error\Handler;

use Throwable;

class StopExecutionHandler implements HandlerInterface
{
    protected $exitCode;

    public function __construct(int $exitCode = 1)
    {
        $this->exitCode = $exitCode;
    }

    public function handle(Throwable $e): void
    {
        exit($this->exitCode);
    }
}
