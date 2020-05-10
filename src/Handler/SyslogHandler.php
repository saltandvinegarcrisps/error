<?php declare(strict_types=1);

namespace Error\Handler;

use Error\Traits;
use Throwable;

class SyslogHandler implements HandlerInterface
{
    use Traits\ExceptionMessage;

    protected $ident;

    public function __construct(string $ident = 'app')
    {
        $this->ident = $ident;
    }

    public function handle(Throwable $e): void
    {
        \openlog($this->ident, LOG_PID | LOG_PERROR, LOG_USER);
        \syslog(LOG_ERR, $this->getMessageWithSource($e));
        \closelog();
    }
}
