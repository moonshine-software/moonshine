<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\WithDefaultValue;
use MoonShine\Traits\Fields\WithEscapedValue;

class Textarea extends Field implements HasDefaultValue, DefaultCanBeString
{
    use WithDefaultValue;
    use WithEscapedValue;

    protected string $view = 'moonshine::fields.textarea';

    protected array $attributes = [
        'rows',
        'cols',
        'disabled',
        'readonly',
        'required',
    ];

    protected function resolvePreview(): View|string
    {
        return $this->isUnescape()
            ? parent::resolvePreview()
            : e((string) parent::resolvePreview());
    }
}
