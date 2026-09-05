<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeString;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Traits\Fields\WithDefaultValue;

class Hidden extends Field implements HasDefaultValueContract, CanBeString
{
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.hidden';

    protected string $type = 'hidden';

    protected bool $hasOld = false;

    protected bool $showValue = false;

    protected bool $columnSelection = false;

    public function __construct(Closure|string|null $label = null, ?string $column = null, ?Closure $formatted = null)
    {
        parent::__construct($label, $column ?? (\is_string($label) ? $label : null), $formatted);
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
            'isShowValue' => $this->isShowValue(),
        ];
    }
}
