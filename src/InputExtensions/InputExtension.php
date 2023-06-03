<?php

declare(strict_types=1);

namespace MoonShine\InputExtensions;

use Illuminate\Support\Collection;
use MoonShine\Traits\WithHtmlAttributes;
use MoonShine\Traits\WithView;

abstract class InputExtension
{
    use WithView;
    use WithHtmlAttributes;

    protected array $xInit = [];

    protected array $xData = [];

    public function __construct(protected mixed $value = null)
    {
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function xData(): Collection
    {
        return collect($this->xData);
    }

    public function xInit(): Collection
    {
        return collect($this->xInit);
    }
}
