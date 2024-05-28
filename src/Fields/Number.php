<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\Components\Rating;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\InputExtensions\InputNumberUpDown;
use MoonShine\Traits\Fields\NumberTrait;

class Number extends Text implements DefaultCanBeNumeric
{
    use NumberTrait;

    protected string $type = 'number';

    protected array $attributes = [
        'type',
        'min',
        'max',
        'step',
        'disabled',
        'readonly',
        'required',
    ];

    protected bool $stars = false;

    public function stars(): static
    {
        $this->stars = true;

        return $this;
    }

    public function withStars(): bool
    {
        return $this->stars;
    }

    public function buttons(): static
    {
        $this->extension(new InputNumberUpDown());

        return $this;
    }

    protected function prepareRequestValue(mixed $value): mixed
    {
        if($this->isGroup()) {
            return $value;
        }

        if(is_float($value)) {
            return (float) parent::prepareRequestValue($value);
        }

        return (int) parent::prepareRequestValue($value);
    }

    protected function resolvePreview(): View|string
    {
        if (! $this->isRawMode() && $this->withStars()) {
            return Rating::make(
                (int) parent::resolvePreview()
            )->render();
        }

        return parent::resolvePreview();
    }
}
