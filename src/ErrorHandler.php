<?php declare(strict_types=1);

namespace Error;

use ErrorException;
use SplObjectStorage;
use Throwable;

class ErrorHandler
{
    protected $listeners;

    protected $previousErrorHandler;

    protected $previousExceptionHandler;

    private $reservedMemory;

    public function __construct(SplObjectStorage $listeners = null)
    {
        $this->listeners = $listeners ?: new SplObjectStorage;
    }

    /**
     * Register callback for handling errors
     *
     * @param int $reservedMemorySize Size in KBs
     * @return void
     */
    public function register(int $reservedMemorySize = 10): void
    {
        $this->reservedMemory = \str_repeat('x', 1024 * $reservedMemorySize);
        $this->previousErrorHandler = \set_error_handler([$this, 'onError']);
        $this->previousExceptionHandler = \set_exception_handler([$this, 'onUncaughtException']);
        \register_shutdown_function([$this, 'onShutdown']);
    }

    /**
     * Add handler
     *
     * @param Handler\HandlerInterface
     * @return void
     */
    public function attach(Handler\HandlerInterface $listener): void
    {
        $this->listeners->attach($listener);
    }

    /**
     * Remove handler
     *
     * @param Handler\HandlerInterface
     * @return void
     */
    public function detach(Handler\HandlerInterface $listener): void
    {
        $this->listeners->detach($listener);
    }

    /**
     * Hand error
     *
     * @param int
     * @param string
     * @param string
     * @param int
     * @return void
     */
    public function onError(int $level, string $message, string $file, int $line): bool
    {
        if (\error_reporting() & $level) {
            $exception = new ErrorException($message, 0, $level, $file, $line);
            $this->onUncaughtException($exception);
        }

        if ($this->previousErrorHandler) {
            $this->previousErrorHandler($level, $message, $file, $line);
        }

        return true;
    }

    /**
     * Handle exception
     *
     * @param Throwable
     * @return void
     */
    public function onUncaughtException(Throwable $exception): void
    {
        if (!$this->listeners->count()) {
            $this->attach(new Handler\EchoHandler);
        }

        foreach ($this->listeners as $listener) {
            try {
                $listener->handle($exception);
            } catch (Throwable $exceptionalException) {
                echo $exceptionalException;
            }
        }

        if ($this->previousExceptionHandler) {
            $this->previousExceptionHandler($exception);
        }
    }

    /**
     * Handle shutdown callback
     *
     * @return void
     */
    public function onShutdown(): void
    {
        if ($error = \error_get_last()) {
            $this->reservedMemory = null;
            $this->onError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
}
