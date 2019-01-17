<?php declare(strict_types=1);

namespace Error\Handler;

use Error\Stacktrace;
use Throwable;

class WebHandler implements HandlerInterface
{
    use ExceptionMessageTrait;

    protected $debug;

    protected $resources;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
        $this->resources = \dirname(__DIR__) . '/Resources';
    }

    protected function getStack(Throwable $e): array
    {
        $stack = [[$e, new Stacktrace($e)]];

        while ($e = $e->getPrevious()) {
            $stack[] = [$e, new Stacktrace($e)];
        }

        return $stack;
    }

    protected function render(Throwable $e): string
    {
        \ob_start();

        $stack = $this->getStack($e);

        require $this->resources.'/'.($this->debug ? 'debug' : 'message').'.html';

        return \ob_get_clean() ?: '';
    }

    public function handle(Throwable $e): void
    {
        \http_response_code(500);
        \header('Content-Type: text/html;charset=utf8');
        echo $this->render($e);
    }
}
