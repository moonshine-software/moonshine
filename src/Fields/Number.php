<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Traits\Fields\NumberTrait;

class Number extends Text implements DefaultCanBeNumeric
{
    use NumberTrait;

    protected string $type = 'number';

    protected string $view = 'moonshine::fields.number';

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

    protected bool $buttons = false;

    public function stars(): static
    {
        $this->stars = true;

        return $this;
    }

    public function buttons(): static
    {
        $this->buttons = true;

        return $this;
    }

    public function withButtons(): bool
    {
        return $this->buttons;
    }

    public function withStars(): bool
    {
        return $this->stars;
    }

    protected function resolvePreview(): View|string
    {
        if (! $this->isRawMode() && $this->withStars()) {
            return view('moonshine::ui.rating', [
                'value' => parent::resolvePreview(),
            ]);
        }

        return parent::resolvePreview();
    }
}
