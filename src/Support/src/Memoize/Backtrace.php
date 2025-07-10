<?php

declare(strict_types=1);

namespace MoonShine\Support\Memoize;

final class Backtrace
{
    /**
     * @var array<string, mixed>
     */
    protected array $trace;

    /**
     * @var array<string, mixed>
     */
    protected array $zeroStack;

    /**
     * @param array<int, array<string, mixed>> $trace
     */
    public function __construct(array $trace)
    {
        $this->trace = $trace[1];

        $this->zeroStack = $trace[0];
    }

    /**
     * @return array<mixed>
     */
    public function getArguments(): array
    {
        return \is_array($this->trace['args']) ? $this->trace['args'] : [];
    }

    public function getFunctionName(): string
    {
        return \is_string($this->trace['function']) ? $this->trace['function'] : '';
    }

    public function getObjectName(): ?string
    {
        if (\is_string($this->trace['class'])) {
            return $this->trace['class'];
        }

        return null;
    }

    public function getObject(): mixed
    {
        if ($this->globalFunction()) {
            return $this->zeroStack['file'];
        }

        return $this->staticCall() ? $this->trace['class'] : $this->trace['object'];
    }

    public function getHash(): string
    {
        $normalizedArguments = array_map(static fn ($argument): mixed => \is_object($argument) ? spl_object_hash($argument) : $argument, $this->getArguments());

        $prefix = $this->getObjectName() . $this->getFunctionName();
        if (str_contains($prefix, '{closure}')) {
            $prefix = $this->zeroStack['line'];
        }

        if (! \is_string($prefix)) {
            $prefix = '';
        }

        return md5($prefix . serialize($normalizedArguments));
    }

    protected function staticCall(): bool
    {
        return $this->trace['type'] === '::';
    }

    protected function globalFunction(): bool
    {
        return ! isset($this->trace['type']);
    }
}
