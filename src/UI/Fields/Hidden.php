<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use MoonShine\UI\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\UI\Contracts\Fields\HasDefaultValue;
use MoonShine\UI\Traits\Fields\WithDefaultValue;

class Hidden extends Field implements HasDefaultValue, DefaultCanBeString
{
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.hidden';

    protected string $type = 'hidden';

    protected bool $showValue = false;

    public function __construct(Closure|string|null $label = null, ?string $column = null, ?Closure $formatted = null)
    {
        parent::__construct($label, $column ?? $label, $formatted);
    }

    public function hasWrapper(): bool
    {
        return false;
    }

    public function showValue(): static
    {
        $this->showValue = true;

        return $this;
    }

    public function isShowValue(): bool
    {
        return $this->showValue;
    }

    protected function viewData(): array
    {
        return [
            ...parent::viewData(),
            'isShowValue' => $this->isShowValue()
        ];
    }
}
