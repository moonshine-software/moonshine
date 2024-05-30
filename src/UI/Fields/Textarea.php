<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use MoonShine\UI\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\UI\Contracts\Fields\HasDefaultValue;
use MoonShine\UI\Traits\Fields\WithDefaultValue;

class Textarea extends Field implements HasDefaultValue, DefaultCanBeString
{
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.textarea';
}
