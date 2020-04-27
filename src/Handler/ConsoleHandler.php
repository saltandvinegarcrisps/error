<?php declare(strict_types=1);

namespace Error\Handler;

use Throwable;

class ConsoleHandler implements HandlerInterface
{
    use ExceptionMessageTrait, ExceptionStackTrait;

    protected function write(string $msg): void
    {
        $stream = \fopen('php://stderr', 'wb');
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
            $this->writeln(\get_class($trace->getException()));
            $this->writeln($trace->getException()->getMessage());
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
