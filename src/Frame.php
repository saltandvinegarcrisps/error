<?php declare(strict_types=1);

namespace Error;

use JsonSerializable;

class Frame implements JsonSerializable
{
    protected $file;

    protected $line;

    protected $caller;

    protected $args = [];

    public static function create(array $params): self
    {
        $frame = new self;

        if (isset($params['file'])) {
            $frame->setFile($params['file']);
        }

        if (isset($params['line'])) {
            $frame->setLine($params['line']);
        }

        if (isset($params['class'])) {
            $frame->setCaller(\sprintf('%s%s%s', $params['class'], $params['type'], $params['function']));
        } elseif (isset($params['function'])) {
            $frame->setCaller($params['function']);
        }

        if (isset($params['args'])) {
            $frame->setArguments($params['args']);
        } else {
            $frame->setArguments([]);
        }

        return $frame;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    public function hasFile(): bool
    {
        return $this->file !== null && \is_readable($this->file);
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function setLine(int $line): void
    {
        $this->line = $line;
    }

    public function hasLine(): bool
    {
        return $this->line !== null;
    }

    public function hasCaller(): bool
    {
        return !empty($this->caller);
    }

    public function getCaller(): string
    {
        return $this->caller;
    }

    public function setCaller(string $caller): void
    {
        $this->caller = $caller;
    }

    public function getArguments(): array
    {
        return $this->args ?: [];
    }

    public function setArguments(array $args): void
    {
        $params = [];

        if ($caller = $this->getCaller()) {
            if (\strpos($caller, '->') !== false) {
                [$class, $method] = \explode('->', $caller);
                $func = (new \ReflectionClass($class))->getMethod($method);
            } else {
                $func = (new \ReflectionFunction($caller));
            }

            if (!$func->isVariadic()) {
                $params = $func->getParameters();
            }
        }

        $this->args = [];

        foreach (\array_values($args) as $index => $arg) {
            $name = \array_key_exists($index, $params) ?
                $params[$index]->getName() : 'param'.($index+1);
            $this->args[$name] = $this->normalise($arg);
        }
    }

    protected function normaliseArray($value): string
    {
        $count = \count($value);

        if ($count < 1 || $count > 100) {
            return 'Array of length ' . $count;
        }

        $types = [];

        foreach ($value as $item) {
            $type = \gettype($item);
            if ('object' === $type) {
                $type = \get_class($item);
            }
            if (!\in_array($type, $types)) {
                $types[] = $type;
            }
        }

        if (\count($types) > 3) {
            return 'Mixed Array of length ' . $count;
        }

        return 'Array<'.\implode('|', $types).'> of length ' . $count;
    }

    protected function normalise($value): string
    {
        if ($value === null) {
            return 'null';
        } elseif ($value === false) {
            return 'false';
        } elseif ($value === true) {
            return 'true';
        } elseif (\is_float($value) && (int) $value == $value) {
            return $value.'.0';
        } elseif (\is_integer($value) || \is_float($value)) {
            return (string) $value;
        } elseif (\is_object($value) || \gettype($value) == 'object') {
            return 'Object '.\get_class($value);
        } elseif (\is_resource($value)) {
            return 'Resource '.\get_resource_type($value);
        } elseif (\is_array($value)) {
            return $this->normaliseArray($value);
        }

        $truncation = new Truncation($value);
        return $truncation->truncate();
    }

    public function hasContext(): bool
    {
        return $this->hasFile() && $this->hasLine();
    }

    public function getContext(): Context
    {
        return new Context($this->getFile(), $this->getLine());
    }

    public function toArray(): array
    {
        $frame = [];

        if ($this->hasFile()) {
            $frame['file'] = $this->getFile();
        }

        if ($this->hasLine()) {
            $frame['line'] = $this->getLine();
        }

        if ($this->hasCaller()) {
            $frame['caller'] = $this->getCaller();
        }

        $frame['args'] = $this->getArguments();

        if ($this->hasContext()) {
            $frame['context'] = $this->getContext()->getPlaceInFile();
        }

        return $frame;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
