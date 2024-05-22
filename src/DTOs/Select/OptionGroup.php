<?php

declare(strict_types=1);

namespace MoonShine\DTOs\Select;

use Illuminate\Contracts\Support\Arrayable;

final readonly class OptionGroup implements Arrayable
{
    public function __construct(
        private string $label,
        private Options $values,
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getValues(): Options
    {
        return $this->values;
    }

    public function toArray(): array
    {
        return [
            'label' => $this->getLabel(),
            'values' => $this->getValues()->toArray(),
        ];
    }
}
