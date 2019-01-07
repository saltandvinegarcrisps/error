<?php declare(strict_types=1);

namespace Error\Handler;

use Throwable;

class EchoHandler implements HandlerInterface
{
    use ExceptionMessageTrait;

    public function handle(Throwable $e): void
    {
        echo $this->getMessage($e);
    }
}
