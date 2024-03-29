<?php

declare(strict_types=1);

namespace Error;

use JsonSerializable;
use function class_exists;

class Frame implements JsonSerializable
{
    protected $file;

    protected $line;

    protected $caller;

    protected $args;

    public function __construct(?string $file = null, ?int $line = null, ?string $caller = null, ?array $args = null)
    {
        $this->file = $file;
        $this->line = $line;
        $this->caller = $caller;
        $this->args = null === $args ? null : $this->createArguments($args);
    }

    public static function create(array $params): self
    {
        if (isset($params['class'], $params['type'], $params['function'])) {
            $params['caller'] = \sprintf('%s%s%s', $params['class'], $params['type'], $params['function']);
        } elseif (isset($params['function'])) {
            $params['caller'] = $params['function'];
        }

        return new self($params['file'] ?? null, $params['line'] ?? null, $params['caller'] ?? null, $params['args'] ?? null);
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function hasFile(): bool
    {
        return $this->file !== null && \is_readable($this->file);
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function hasLine(): bool
    {
        return $this->line !== null;
    }

    public function hasCaller(): bool
    {
        return null !== $this->caller;
    }

    public function getCaller(): ?string
    {
        return $this->caller;
    }

    public function getArguments(): ?array
    {
        return $this->args;
    }

    public function hasArgument(): bool
    {
        return is_array($this->args) && count($this->args) > 0;
    }

    protected function getParams(): array
    {
        $params = [];
        $caller = $this->getCaller();

        if (null === $caller) {
            return $params;
        }

        if (\strpos($caller, '->') || \strpos($caller, '::')) {
            [$class, $method] = \explode(' ', \str_replace(['->', '::'], ' ', $caller));
            if (!class_exists($class)) {
                return $params;
            }
            try {
                $func = (new \ReflectionClass($class))->getMethod($method);
            } catch (\ReflectionException  $e) {
                return $params;
            }
        } else {
            try {
                $func = (new \ReflectionFunction($caller));
            } catch (\ReflectionException  $e) {
                return $params;
            }
        }

        if (!$func->isVariadic()) {
            $params = $func->getParameters();
        }

        return $params;
    }

    protected function createArguments(array $args): array
    {
        $paramNames = $this->getParams();
        $normalised = [];

        foreach (\array_values($args) as $index => $arg) {
            $name = \array_key_exists($index, $paramNames) ?
                $paramNames[$index]->getName() : 'param'.($index+1);
            $normalised[$name] = $this->normalise($arg);
        }

        return $normalised;
    }

    protected function normaliseArray(array $value, int $max = 10): string
    {
        $values = [];

        $sequential = $value === array_values($value);

        foreach ($value as $key => $value) {
            $values[] = $sequential ?
                $this->normalise($value) :
                sprintf('%s: %s', $this->normalise($key), $this->normalise($value))
            ;
            if (count($values) === $max) {
                $values[] = sprintf('%u more...', count($values) - $max);
            }
        }

        return sprintf('[%s]', implode(', ', $values));
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

        return (new Truncation($value))->truncate();
    }

    public function hasContext(): bool
    {
        return $this->hasFile() && $this->hasLine();
    }

    public function getContext(): Context
    {
        return new Context($this->getFile(), $this->getLine());
    }

    public function toString(): string
    {
        if ($this->hasContext()) {
            return $this->getFile().':'.$this->getLine();
        }
        return $this->getCaller() ?? '';
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
