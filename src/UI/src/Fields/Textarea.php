<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Contracts\Support\Renderable;
use MoonShine\Support\Enums\TextWrap;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeString;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Fields\WithEscapedValue;

class Textarea extends Field implements HasDefaultValueContract, CanBeString
{
    use HasPlaceholder;
    use WithDefaultValue;
    use WithEscapedValue;

    protected string $view = 'moonshine::fields.textarea';

    protected ?TextWrap $textWrap = TextWrap::CLAMP;

    protected function resolvePreview(): Renderable|string
    {
        return $this->isUnescape()
            ? parent::resolvePreview()
            : $this->escapeValue((string) parent::resolvePreview());
    }

    protected function prepareRequestValue(mixed $value): mixed
    {
        if (\is_string($value) && static::class === self::class) {
            return $this->isUnescape() ? $value : $this->escapeValue($value);
        }

        return $value;
    }

    protected function resolveValue(): mixed
    {
        if (! $this->isUnescape() && static::class === self::class) {
            return $this->escapeValue(
                parent::resolveValue()
            );
        }

        return parent::resolveValue();
    }
}
