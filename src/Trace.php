<?php declare(strict_types=1);

namespace Error;

use Throwable;

class Trace
{
    /**
     * @var Throwable
     */
    protected $exception;

    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function getException(): Throwable
    {
        return $this->exception;
    }

    public function getContext(): Context
    {
        return new Context($this->exception->getFile(), $this->exception->getLine());
    }

    protected function containsSource(array $frame): bool
    {
        return isset($frame['file'], $frame['file']) &&
            $frame['file'] == $this->exception->getFile() &&
            $frame['line'] == $this->exception->getLine();
    }

    protected function getBacktrace(): array
    {
        $trace = $this->exception->getTrace();

        if (!$this->containsSource($trace[0])) {
            array_unshift($trace, [
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
            ]);
        }

        return $trace;
    }

    public function getFrames(): array
    {
        $frames = [];

        foreach ($this->getBacktrace() as $params) {
            $frames[] = Frame::create($params);
        }

        return $frames;
    }
}
