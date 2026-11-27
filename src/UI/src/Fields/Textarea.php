<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Contracts\Support\Renderable;
use MoonShine\Support\Enums\TextWrap;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeString;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\HasEscapedValueContract;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Fields\WithEscapedValue;
use MoonShine\UI\Traits\Fields\WithPrettyLimit;

class Textarea extends Field implements HasDefaultValueContract, CanBeString, HasEscapedValueContract
{
    use HasPlaceholder;
    use WithDefaultValue;
    use WithEscapedValue;
    use WithPrettyLimit;

    protected string $view = 'moonshine::fields.textarea';

    protected ?TextWrap $textWrap = TextWrap::CLAMP;

    protected function resolvePreview(): Renderable|string
    {
        return $this->isUnescape()
            ? parent::resolvePreview()
            : $this->escapeValue(
                $this->decodeEscapedOnApplyValue($this->stringifySlotContent(parent::resolvePreview()))
            );
    }

    /**
     * Subclasses (editors) handle their own request values and are saved unescaped.
     */
    protected function isEscapedOnApply(): bool
    {
        return static::class === self::class && ! $this->isUnescapeOnApply();
    }

    protected function prepareRequestValue(mixed $value): mixed
    {
        if (\is_string($value) && $this->isEscapedOnApply()) {
            return $this->escapeValue($value);
        }

        return $value;
    }

    protected function resolveValue(): mixed
    {
        if (! $this->isUnescape() && static::class === self::class) {
            return $this->escapeValue(
                $this->decodeEscapedOnApplyValue($this->stringifySlotContent(parent::resolveValue()))
            );
        }

        return parent::resolveValue();
    }
}
