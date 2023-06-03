<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\WithDefaultValue;

class Textarea extends Field implements HasDefaultValue, DefaultCanBeString
{
    use WithDefaultValue;

    protected static string $view = 'moonshine::fields.textarea';

    protected array $attributes = [
        'rows',
        'cols',
        'disabled',
        'readonly',
        'required',
    ];
}
