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

    public function getFrames(): array
    {
        $trace = $this->exception->getTrace();

        $containsException = \array_reduce($trace, function (bool $carry, array $frame) {
            if (
                \array_key_exists('file', $frame) &&
                \array_key_exists('line', $frame) &&
                $frame['file'] === $this->exception->getFile() &&
                $frame['line'] === $this->exception->getLine()
            ) {
                return true;
            }
            return $carry;
        }, false);

        if (!$containsException) {
            \array_unshift($trace, [
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
                'function' => '{main}',
            ]);
        }

        $frames = [];

        foreach ($trace as $params) {
            $frames[] = Frame::create($params);
        }

        return $frames;
    }
}
