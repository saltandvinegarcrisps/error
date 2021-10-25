<?php

declare(strict_types=1);

namespace Error\Handler;

use Error\Traits;
use Throwable;

class WebHandler implements HandlerInterface
{
    use Traits\ExceptionMessage;
    use Traits\ExceptionStack;

    protected $debug;

    protected $resources;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
        $this->resources = __DIR__ . '/../../resources';
    }

    protected function render(Throwable $e): string
    {
        \ob_start();

        $stack = $this->getStack($e);

        require $this->resources.'/'.($this->debug ? 'debug' : 'message').'.php';

        return \ob_get_clean() ?: '';
    }

    public function handle(Throwable $e): void
    {
        if (!\headers_sent()) {
            \header('Content-Type: text/html', true, 500);
        }
        echo $this->render($e);
    }
}
