<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\HasPlaceholder;
use MoonShine\Traits\Fields\NumberTrait;
use MoonShine\Traits\Fields\UpdateOnPreview;
use MoonShine\Traits\Fields\WithDefaultValue;
use MoonShine\Traits\Fields\WithInputExtensions;
use MoonShine\Traits\Fields\WithMask;

class Number extends Field implements DefaultCanBeNumeric, HasDefaultValue
{
    use NumberTrait;
    use WithInputExtensions;
    use WithMask;
    use WithDefaultValue;
    use HasPlaceholder;
    use UpdateOnPreview;

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

    public function hasButtons(): bool
    {
        return $this->buttons;
    }

    public function asStars(): bool
    {
        return $this->stars;
    }

    protected function resolvePreview(): View|string
    {
        if (! $this->isRawMode() && $this->asStars()) {
            return view('moonshine::ui.rating', [
                'value' => parent::resolvePreview(),
            ]);
        }

        return parent::resolvePreview();
    }
}
