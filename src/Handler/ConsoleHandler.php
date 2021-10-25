<?php

declare(strict_types=1);

namespace Error\Handler;

use Error\Traits;
use Throwable;

class ConsoleHandler implements HandlerInterface
{
    use Traits\ExceptionMessage;
    use Traits\ExceptionStack;

    protected function write(string $msg): void
    {
        $stream = \fopen('php://stderr', 'wb');
        if (false === $stream) {
            throw new HandlerException('Failed to open steam');
        }
        \fwrite($stream, $msg);
    }

    protected function writeln(string $msg): void
    {
        $this->write($msg."\n");
    }

    public function handle(Throwable $e): void
    {
        $stack = $this->getStack($e);
        $this->writeln('');
        foreach ($stack as $trace) {
            $this->writeln($this->getMessage($trace->getException()));
            $this->writeln('');
            foreach ($trace->getFrames() as $index => $frame) {
                if (!$frame->hasFile()) {
                    continue;
                }
                $this->writeln('    '.$frame->getFile().':'.$frame->getLine());
                if ($index === 0) {
                    $this->writeln('');
                    $lines = $frame->getContext()->getPlaceInFile();
                    foreach ($lines as $num => $line) {
                        if ($num === $frame->getLine()) {
                            $this->writeln('    --> '.$num.' '.rtrim($line));
                        } else {
                            $this->writeln('        '.$num.' '.rtrim($line));
                        }
                    }
                    $this->writeln('');
                }
                if ($frame->hasArgument()) {
                    foreach ($frame->getArguments() as $key => $value) {
                        $this->writeln('        '.$key.' '.rtrim($value));
                    }
                    $this->writeln('');
                }
            }
        }
        $this->writeln('');
    }
}
