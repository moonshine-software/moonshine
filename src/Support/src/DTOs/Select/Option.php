<?php

declare(strict_types=1);

namespace MoonShine\Support\DTOs\Select;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithComponentAttributes;

/**
 * @method static static make(string $label, string $value, bool $selected = false, null|OptionProperty $properties = null)
 *
 * @implements Arrayable<string, mixed>
 */
final class Option implements Arrayable
{
    use Makeable;
    use WithComponentAttributes;

    public function __construct(
        private readonly string $label,
        private readonly string $value,
        private readonly bool $selected = false,
        private readonly ?OptionProperty $properties = null
    ) {
        $this->attributes = new MoonShineComponentAttributeBag();
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

    public function disabled(Closure|bool|null $condition = null): self
    {
        return $this->customAttributes([
            'disabled' => value($condition, $this) ?? true,
        ]);
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
            'attributes' => $this->getAttributes(),
        ];
    }
}
