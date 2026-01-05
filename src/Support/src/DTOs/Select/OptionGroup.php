<?php

declare(strict_types=1);

namespace MoonShine\Support\DTOs\Select;

use Illuminate\Contracts\Support\Arrayable;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithComponentAttributes;

/**
 * @method static static make(string $label, Options $values)
 *
 * @implements Arrayable<string, mixed>
 */
final class OptionGroup implements Arrayable
{
    use Makeable;
    use WithComponentAttributes;

    public function __construct(
        private readonly string $label,
        private readonly Options $values,
    ) {
        $this->attributes = new MoonShineComponentAttributeBag();
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getValues(): Options
    {
        return $this->values;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'label' => $this->getLabel(),
            'values' => $this->getValues()->toArray(),
            'attributes' => $this->getAttributes(),
        ];
    }
}
