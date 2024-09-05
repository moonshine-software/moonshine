<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\UI\HasReactivityContract;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeString;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\Reactivity;
use MoonShine\UI\Traits\Fields\UpdateOnPreview;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\Fields\WithEscapedValue;
use MoonShine\UI\Traits\Fields\WithInputExtensions;
use MoonShine\UI\Traits\Fields\WithMask;

class Text extends Field implements HasDefaultValueContract, CanBeString, HasUpdateOnPreviewContract, HasReactivityContract
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
            return $this->isUnescape() ? $value : $this->escapeValue($value);
        }

        return $value;
    }

    protected function resolvePreview(): Renderable|string
    {
        return $this->isUnescape()
            ? parent::resolvePreview()
            : $this->escapeValue((string) parent::resolvePreview());
    }

    protected function viewData(): array
    {
        return [
            ...$this->getExtensionsViewData(),
        ];
    }
}
