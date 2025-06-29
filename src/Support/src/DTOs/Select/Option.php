<?php

declare(strict_types=1);

namespace MoonShine\Support\DTOs\Select;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
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
        return htmlspecialchars(
            $this->label,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8',
        );
    }

    public function getValue(): string
    {
        return htmlspecialchars(
            $this->value,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8',
        );
    }

    public function isSelected(): bool
    {
        return $this->selected;
    }

    public function getProperties(): ?OptionProperty
    {
        return $this->properties;
    }

    /**
     * @return array<string, mixed>
     */
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
