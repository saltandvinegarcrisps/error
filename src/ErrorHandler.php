<?php

declare(strict_types=1);

namespace Error;

use ErrorException;
use SplObjectStorage;
use Throwable;

class ErrorHandler
{
    /**
     * @var int
     */
    protected $fatalErrors = E_ERROR | E_USER_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_PARSE;

    /**
     * @var SplObjectStorage
     */
    protected $listeners;

    /**
     * @var string|null
     */
    protected $reservedMemory;

    public function __construct(?SplObjectStorage $listeners = null)
    {
        $this->listeners = $listeners ?? new SplObjectStorage();
    }

    /**
     * Register callback for handling errors
     *
     * @param int $reservedMemorySize Size in KBs
     * @return void
     */
    public function register(int $reservedMemorySize = 10): void
    {
        if ($reservedMemorySize < 0) {
            $reservedMemorySize = 0;
        }
        $this->reservedMemory = str_repeat(' ', 1024 * $reservedMemorySize);
        set_error_handler([$this, 'onError']);
        set_exception_handler([$this, 'onException']);
        register_shutdown_function([$this, 'onShutdown']);
    }

    /**
     * Remove callbacks
     *
     * @return void
     */
    public function deregister(): void
    {
        restore_error_handler();
        restore_exception_handler();
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
     * Handle error
     *
     * @param int
     * @param string
     * @param string
     * @param int
     * @return bool
     */
    public function onError(int $level, string $message, string $file, int $line): bool
    {
        if ($level & error_reporting()) {
            $this->onException(new ErrorException($message, 0, $level, $file, $line));
            if ($level & $this->fatalErrors) {
                $this->terminate();
            }
            return true;
        }
        return false;
    }

    /**
     * Handle exception
     *
     * @param Throwable
     * @return void
     */
    public function onException(Throwable $exception): void
    {
        if (!$this->listeners->count()) {
            $this->attach(new Handler\EchoHandler());
        }

        try {
            foreach ($this->listeners as $listener) {
                if (!method_exists($listener, 'handle')) {
                    continue;
                }
                $listener->handle($exception);
            }
        } catch (Throwable $exceptionalException) {
            restore_exception_handler();
            throw $exceptionalException;
        }
    }

    /**
     * Handle shutdown callback
     *
     * @return void
     */
    public function onShutdown(): void
    {
        $this->reservedMemory = null;
        $error = error_get_last();
        if ($error && ($error['type'] & $this->fatalErrors)) {
            $this->onError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    /**
     * Halt execution
     *
     * @return void
     */
    protected function terminate(): void
    {
        exit(1);
    }
}
