<?php declare(strict_types=1);

namespace Error;

class Truncation
{
    protected $payload;

    protected $threshold;

    public function __construct(string $payload, int $threshold = 1024)
    {
        $this->payload = $payload;
        $this->threshold = $threshold;
    }

    public function truncate(): string
    {
        $size = \mb_strlen($this->payload);

        if ($size > $this->threshold) {
            $newPayload = \mb_substr($this->payload, 0, $this->threshold);
            $truncated = $size - $this->threshold;
            return \sprintf('%s .. (truncated %d)', $newPayload, $truncated);
        }

        return $this->payload;
    }
}
