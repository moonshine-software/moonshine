<?php

declare(strict_types=1);

namespace Leeto\MoonShine\InputExtensions;

use Illuminate\Support\Collection;
use Leeto\MoonShine\Traits\WithHtmlAttributes;
use Leeto\MoonShine\Traits\WithView;

abstract class InputExtension
{
    use WithHtmlAttributes;
    use WithView;

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
