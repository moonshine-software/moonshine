<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use MoonShine\Core\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Core\Contracts\Fields\HasDefaultValue;
use MoonShine\UI\Traits\Fields\WithDefaultValue;

class Hidden extends Field implements HasDefaultValue, DefaultCanBeString
{
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.hidden';

    protected string $type = 'hidden';

    public function __construct(Closure|string|null $label = null, ?string $column = null, ?Closure $formatted = null)
    {
        parent::__construct($label, $column ?? $label, $formatted);
    }

    public function hasWrapper(): bool
    {
        return false;
    }
}
