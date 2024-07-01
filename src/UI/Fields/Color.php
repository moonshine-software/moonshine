<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use MoonShine\UI\Components\Color as ColorComponent;
use MoonShine\UI\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\UI\Contracts\Fields\HasDefaultValue;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use Illuminate\Contracts\Support\Renderable;

class Color extends Field implements HasDefaultValue, DefaultCanBeString
{
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.color';

    protected string $type = 'color';

    protected function resolvePreview(): Renderable|string
    {
        if($this->isRawMode()) {
            return $this->toValue();
        }

        return ColorComponent::make($this->getValue())
            ->render();
    }
}
