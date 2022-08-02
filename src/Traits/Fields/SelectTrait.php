<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

trait SelectTrait
{
    protected array $options = [];

    public function options(array $data): static
    {
        $this->options = $data;

        return $this;
    }

    public function values(): array
    {
        return $this->options;
    }

    public function isSelected(string $value): bool
    {
        if (!$this->value()) {
            return false;
        }

        return (string) $this->value() === $value
            || (!$this->value() && (string) $this->getDefault() === $value);
    }
}
