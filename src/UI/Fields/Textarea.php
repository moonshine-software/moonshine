<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use MoonShine\UI\Contracts\DefaultValueTypes\CanBeString;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Traits\Fields\WithDefaultValue;

class Textarea extends Field implements HasDefaultValueContract, CanBeString
{
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.textarea';
}
