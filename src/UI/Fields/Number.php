<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\UI\Components\Rating;
use MoonShine\UI\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\UI\Contracts\Fields\HasDefaultValue;
use MoonShine\UI\Contracts\Fields\HasReactivity;
use MoonShine\UI\Contracts\Fields\HasUpdateOnPreview;
use MoonShine\UI\InputExtensions\InputNumberUpDown;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\NumberTrait;
use MoonShine\UI\Traits\Fields\Reactivity;
use MoonShine\UI\Traits\Fields\UpdateOnPreview;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Fields\WithInputExtensions;

class Number extends Field implements HasDefaultValue, DefaultCanBeNumeric, HasUpdateOnPreview, HasReactivity
{
    use NumberTrait;
    use WithInputExtensions;
    use WithDefaultValue;
    use HasPlaceholder;
    use UpdateOnPreview;
    use Reactivity;

    protected string $view = 'moonshine::fields.input';

    protected string $type = 'number';

    protected array $propertyAttributes = [
        'type',
        'min',
        'max',
        'step',
    ];

    public function buttons(): static
    {
        $this->extension(new InputNumberUpDown());

        return $this;
    }

    protected function prepareRequestValue(mixed $value): mixed
    {
        if(is_null($value)) {
            return parent::prepareRequestValue($value);
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

    protected function viewData(): array
    {
        return [
            'extensions' => $this->getExtensions(),
        ];
    }
}
