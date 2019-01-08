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

    protected function getTrace(): array
    {
        $trace = $this->exception->getTrace();

        if (empty($trace)) {
            return [[
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
                'function' => '{main}',
            ]];
        }

        return $trace;
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
