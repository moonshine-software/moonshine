<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

use Illuminate\Database\Eloquent\Model;

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

    public function isSelected(Model $item, string $value): bool
    {
        if (!$this->formViewValue($item)) {
            return false;
        }

        return (string)$this->formViewValue($item) === $value
            || (!$this->formViewValue($item) && (string)$this->getDefault() === $value);
    }
}
