<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasReactivity;
use MoonShine\Contracts\Fields\HasUpdateOnPreview;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\Reactivity;
use MoonShine\UI\Traits\Fields\UpdateOnPreview;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Fields\WithInputExtensions;
use MoonShine\UI\Traits\Fields\WithMask;

class Text extends Field implements HasDefaultValue, DefaultCanBeString, HasUpdateOnPreview, HasReactivity
{
    use WithInputExtensions;
    use WithMask;
    use WithDefaultValue;
    use HasPlaceholder;
    use UpdateOnPreview;
    use Reactivity;

    protected string $view = 'moonshine::fields.input';

    protected string $type = 'text';

    public function tags(?int $limit = null): static
    {
        return $this->customAttributes([
            'x-data' => 'select',
            'data-max-item-count' => $limit,
            'data-remove-item-button' => true,
        ]);
    }

    protected function viewData(): array
    {
        return [
            'extensions' => $this->getExtensions(),
        ];
    }
}
