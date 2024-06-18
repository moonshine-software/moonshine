<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\UI\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\UI\Contracts\Fields\HasDefaultValue;
use MoonShine\UI\Contracts\Fields\HasReactivity;
use MoonShine\UI\Contracts\Fields\HasUpdateOnPreview;
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

    protected bool $unescape = false;

    public function tags(?int $limit = null): static
    {
        return $this->customAttributes([
            'x-data' => 'select',
            'data-max-item-count' => $limit,
            'data-remove-item-button' => true,
        ]);
    }

    public function unescape(): static
    {
        $this->unescape = true;

        return $this;
    }

    public function isUnescape(): bool
    {
        return $this->unescape;
    }

    protected function prepareRequestValue(mixed $value): mixed
    {
        if(is_string($value)) {
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

    protected function viewData(): array
    {
        return [
            ...$this->getExtensionsViewData(),
        ];
    }
}
