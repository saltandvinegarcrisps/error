<?php declare(strict_types=1);

namespace Error;

use Throwable;

class Stacktrace
{
    /**
     * @var Throwable
     */
    protected $exception;

    /**
     * @param Throwable
     */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    /**
     * @return array
     */
    protected function getTrace(): array
    {
        $frames = $this->exception->getTrace();

        // if (!isset($frames[0]['file']) || $frames[0]['file'] !== $this->exception->getFile()) {
        //     \array_unshift($frames, [
        //         'file' => $this->exception->getFile(),
        //         'line' => $this->exception->getLine(),
        //     ]);
        // }

        return $frames;
    }

    /**
     * @return array
     */
    public function getFrames(): array
    {
        $frames = [];

        foreach ($this->getTrace() as $params) {
            $frames[] = Frame::create($params);
        }

        return $frames;
    }
}
