<?php declare(strict_types=1);

namespace Error\Handler;

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
        $this->logger->error($this->getMessage($e), ['exception' => $e]);
    }
}
