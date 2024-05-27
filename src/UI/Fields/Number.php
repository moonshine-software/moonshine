<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\UI\Components\Rating;
use MoonShine\UI\InputExtensions\InputNumberUpDown;
use MoonShine\UI\Traits\Fields\NumberTrait;

class Number extends Text implements DefaultCanBeNumeric
{
    use NumberTrait;

    protected string $type = 'number';

    protected array $propertyAttributes = [
        'type',
        'min',
        'max',
        'step',
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
