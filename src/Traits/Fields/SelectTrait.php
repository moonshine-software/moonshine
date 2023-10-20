<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use JsonException;
use MoonShine\Support\SelectOptions;

trait SelectTrait
{
    protected array $options = [];

    protected array|Closure $optionProperties = [];

    public function options(Closure|array $data): static
    {
        $this->options = is_closure($data)
            ? $data($this)
            : $data;

        return $this;
    }

    public function values(): array
    {
        return $this->options;
    }

    /**
     * @throws JsonException
     */
    public function isSelected(string $value): bool
    {
        return SelectOptions::isSelected($this->value(), $value);
    }

    public function optionProperties(Closure|array $data): static
    {
        $this->optionProperties = $data;

        return $this;
    }

    public function getOptionProperties(string $value): array
    {
        return data_get(value($this->optionProperties), $value, []);
    }

    public function flattenValues(): array
    {
        return SelectOptions::flatten($this->values());
    }
}
