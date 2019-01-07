<?php declare(strict_types=1);

namespace Error\Handler;

use Throwable;

class CallbackHandler implements HandlerInterface
{
    protected $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function handle(Throwable $exception): void
    {
        \call_user_func($this->callable, $exception);
    }
}
