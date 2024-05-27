<?php

declare(strict_types=1);

namespace MoonShine\Support\DTOs\Select;

use Illuminate\Contracts\Support\Arrayable;

final readonly class Option implements Arrayable
{
    public function __construct(
        private string $label,
        private string $value,
        private bool $selected = false,
        private ?OptionProperty $properties = null
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isSelected(): bool
    {
        return $this->selected;
    }

    public function getProperties(): ?OptionProperty
    {
        return $this->properties;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->getValue(),
            'label' => $this->getLabel(),
            'selected' => $this->isSelected(),
            'properties' => $this->getProperties()?->toArray() ?? [],
        ];
    }
}
