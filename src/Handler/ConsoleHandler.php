<?php declare(strict_types=1);

namespace Error\Handler;

use Throwable;

class ConsoleHandler implements HandlerInterface
{
    use ExceptionMessageTrait, ExceptionStackTrait;

    protected function write(string $msg): void
    {
        \fwrite(STDERR, $msg);
    }

    protected function writeln(string $msg): void
    {
        $this->write($msg."\n");
    }

    public function handle(Throwable $e): void
    {
        $stack = $this->getStack($e);
        $this->writeln('');
        foreach ($stack as [$exception, $trace]) {
            $this->writeln(\get_class($exception));
            $this->writeln($exception->getMessage());
            $this->writeln('');
            foreach ($trace->getFrames() as $frame) {
                if (!$frame->hasFile()) {
                    continue;
                }
                $this->writeln('    '.$frame->getFile().':'.$frame->getLine());
                $this->writeln('');
                $lines = $frame->getContext()->getPlaceInFile();
                foreach ($lines as $num => $line) {
                    $this->writeln('        '.$num.' '.$line);
                }
                $this->writeln('');
                break;
            }
        }
    }
}
