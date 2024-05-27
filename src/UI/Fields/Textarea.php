<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use MoonShine\Core\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Core\Contracts\Fields\HasDefaultValue;
use MoonShine\UI\Traits\Fields\WithDefaultValue;

class Textarea extends Field implements HasDefaultValue, DefaultCanBeString
{
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.textarea';
}
