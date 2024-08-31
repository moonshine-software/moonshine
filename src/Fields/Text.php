<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasUpdateOnPreview;
use MoonShine\Contracts\HasReactivity;
use MoonShine\Traits\Fields\HasPlaceholder;
use MoonShine\Traits\Fields\Reactivity;
use MoonShine\Traits\Fields\UpdateOnPreview;
use MoonShine\Traits\Fields\WithDefaultValue;
use MoonShine\Traits\Fields\WithEscapedValue;
use MoonShine\Traits\Fields\WithInputExtensions;
use MoonShine\Traits\Fields\WithMask;

class Text extends Field implements HasDefaultValue, DefaultCanBeString, HasUpdateOnPreview, HasReactivity
{
    use WithInputExtensions;
    use WithMask;
    use WithDefaultValue;
    use HasPlaceholder;
    use UpdateOnPreview;
    use Reactivity;
    use WithEscapedValue;

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

    protected function prepareRequestValue(mixed $value): mixed
    {
        if (is_string($value)) {
            return $this->isUnescape() ? $value : e($value);
        }

        return $value;
    }

    protected function resolvePreview(): View|string
    {
        return $this->isUnescape()
            ? parent::resolvePreview()
            : e((string) parent::resolvePreview());
    }
}
