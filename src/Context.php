<?php

declare(strict_types=1);

namespace Error;

use LimitIterator;
use SplFileObject;

class Context
{
    protected $file;

    protected $line;

    /**
     * @param string
     * @param int
     */
    public function __construct(string $file, int $line)
    {
        $this->file = $file;
        $this->line = $line;
    }

    /**
     * @param int
     * @param int
     * @return array
     */
    public function getPlaceInFile(int $linesBefore = 4, int $linesAfter = 4): array
    {
        $context = [];

        $offset = (int) ($this->line - $linesBefore - 1);

        if ($offset < 0) {
            $linesBefore = 0;
            $offset = 0;
        }

        $file = new SplFileObject($this->file, 'rb');
        $iterator = new LimitIterator($file, $offset, $linesBefore + $linesAfter + 1);
        $index = $offset + 1;

        foreach ($iterator as $text) {
            $context[$index] = $text;
            $index++;
        }

        return $context;
    }
}
