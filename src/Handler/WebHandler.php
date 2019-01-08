<?php declare(strict_types=1);

namespace Error\Handler;

use Error\Context;
use Error\Stacktrace;
use Throwable;

class WebHandler implements HandlerInterface
{
    use ExceptionMessageTrait;

    protected $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    public function handle(Throwable $e): void
    {
        \http_response_code(500);
        \header('Content-Type: text/html;charset=utf8');

        $stack = [[$e, new Stacktrace($e)]];

        while ($e = $e->getPrevious()) {
            $stack[] = [$e, new Stacktrace($e)];
        }

        $file = __DIR__ . '/../Resources/'.($this->debug ? 'debug' : 'message').'.html';

        require $file;
    }
}
