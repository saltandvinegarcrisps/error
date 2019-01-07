<?php declare(strict_types=1);

namespace Error;

use ErrorException;
use SplObjectStorage;
use Throwable;

class ErrorHandler
{
    protected $listeners;

    public function __construct(SplObjectStorage $listeners = null)
    {
        $this->listeners = $listeners ?: new SplObjectStorage;
    }

    /**
     * Register callback for handling errors
     *
     * @return void
     */
    public function register(): void
    {
        \set_error_handler([$this, 'onError']);
        \set_exception_handler([$this, 'onUncaughtException']);
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
    public function onError(int $level, string $message, string $file, int $line): void
    {
        if (\error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
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
            $listener->handle($exception);
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
            throw new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
        }
    }
}
