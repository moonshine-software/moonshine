<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\UI\HasReactivityContract;
use MoonShine\UI\Components\Rating;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeNumeric;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\InputExtensions\InputNumberUpDown;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\NumberTrait;
use MoonShine\UI\Traits\Fields\Reactivity;
use MoonShine\UI\Traits\Fields\UpdateOnPreview;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Fields\WithInputExtensions;

class Number extends Field implements HasDefaultValueContract, CanBeNumeric, HasUpdateOnPreviewContract, HasReactivityContract
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

    protected function resolvePreview(): Renderable|string
    {
        if ($this->isWithStars()) {
            return Rating::make(
                (int) parent::resolvePreview()
            )->render();
        }

        return parent::resolvePreview();
    }

    protected function viewData(): array
    {
        return [
            ...$this->getExtensionsViewData(),
        ];
    }
}
