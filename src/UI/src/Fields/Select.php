<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use JsonException;
use MoonShine\Contracts\UI\HasAsyncContract;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeArray;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeNumeric;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeString;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\Traits\Fields\CanBeMultiple;
use MoonShine\UI\Traits\Fields\ConfigurableSelect;
use MoonShine\UI\Traits\Fields\HasPlaceholder;
use MoonShine\UI\Traits\Fields\Searchable;
use MoonShine\UI\Traits\Fields\SelectTrait;
use MoonShine\UI\Traits\Fields\UpdateOnPreview;
use MoonShine\UI\Traits\Fields\WithDefaultValue;
use MoonShine\UI\Traits\HasAsync;

class Select extends Field implements
    HasDefaultValueContract,
    CanBeArray,
    CanBeString,
    CanBeNumeric,
    HasUpdateOnPreviewContract,
    HasAsyncContract
{
    use CanBeMultiple;
    use Searchable;
    use SelectTrait;
    use WithDefaultValue;
    use HasAsync;
    use UpdateOnPreview;
    use HasPlaceholder;
    use ConfigurableSelect;

    protected string $view = 'moonshine::fields.select';

    protected function resolveRawValue(): mixed
    {
        return $this->resolvePreview();
    }

    /**
     * @throws JsonException
     */
    protected function resolvePreview(): string
    {
        $value = $this->toValue();

        if ($this->isMultiple()) {
            return $this->getMultiplePreview($value);
        }

        if (\is_null($value)) {
            return '';
        }

        return (string)data_get($this->getValues()->flatten(), "$value.label", '');
    }

    public function asyncOnInit(bool $whenOpen = true, bool $withLoading = false): static
    {
        return $this->customAttributes([
            'data-async-on-init' => true,
            'data-async-on-init-dropdown' => $whenOpen,
            'data-async-on-init-dropdown-with-loading' => $whenOpen && $withLoading,
        ]);
    }

    protected function asyncWith(): void
    {
        $this->searchable();
        $this->asyncSettings([]);
    }

    public function prepareReactivityValue(mixed $value, mixed &$casted, array &$except): mixed
    {
        $result = data_get($value, 'value', $value);

        return $this->isMultiple() && \is_array($result)
            ? array_filter($result, static fn ($value): bool => $value !== null && $value !== false)
            : $result;
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        if (! $this->getAttributes()->has('data-validation-field')) {
            $this->customAttributes([
                'data-validation-field' => preg_replace("/\[\d*]$/", '', $this->getNameAttribute()),
            ]);
        }
    }

    protected function viewData(): array
    {
        return [
            'asyncUrl' => $this->getAsyncUrl(),
            'values' => $this->getValues()->toArray(),
            'isNullable' => $this->isNullable(),
            ...$this->getSelectViewData(),
        ];
    }
}
