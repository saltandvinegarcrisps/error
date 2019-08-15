<?php declare(strict_types=1);

namespace Error\Handler;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Throwable;

class PsrHandler implements HandlerInterface
{
    use ExceptionMessageTrait;

    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $e): void
    {
        if ($e instanceof \ErrorException) {
            $this->handleErrorException($e);
            return;
        }

        $this->logger->error($this->getMessage($e), ['exception' => $e]);
    }

    protected function handleErrorException(\ErrorException $e) {
        switch ($e->getSeverity()) {
            case E_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_PARSE:
                $this->logger->error($this->getMessage($e), ['exception' => $e]);
                break;
            case E_WARNING:
            case E_USER_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
                $this->logger->warning($this->getMessage($e), ['exception' => $e]);
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $this->logger->notice($this->getMessage($e), ['exception' => $e]);
                break;
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $this->logger->info($this->getMessage($e), ['exception' => $e]);
                break;
        }
    }
}
