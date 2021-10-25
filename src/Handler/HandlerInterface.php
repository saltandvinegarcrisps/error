<?php

declare(strict_types=1);

namespace Error\Handler;

use Throwable;

interface HandlerInterface
{
    public function handle(Throwable $exception): void;
}
