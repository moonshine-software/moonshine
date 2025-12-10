<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Support\AlpineJs;
use MoonShine\UI\Components\Boolean;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeBool;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeNumeric;
use MoonShine\UI\Contracts\DefaultValueTypes\CanBeString;
use MoonShine\UI\Contracts\HasDefaultValueContract;
use MoonShine\UI\Contracts\HasUpdateOnPreviewContract;
use MoonShine\UI\Traits\Fields\BooleanTrait;
use MoonShine\UI\Traits\Fields\UpdateOnPreview;
use MoonShine\UI\Traits\Fields\WithDefaultValue;

class Checkbox extends Field implements
    HasDefaultValueContract,
    CanBeNumeric,
    CanBeString,
    CanBeBool,
    HasUpdateOnPreviewContract
{
    use BooleanTrait;
    use WithDefaultValue;
    use UpdateOnPreview;

    protected string $view = 'moonshine::fields.checkbox';

    protected string $type = 'checkbox';

    protected bool $simpleMode = false;

    public function isChecked(): bool
    {
        if ($this->isSimpleMode()) {
            return false;
        }

        return $this->getOnValue() == $this->getValue();
    }

    public function simpleMode(): static
    {
        $this->simpleMode = true;

        return $this;
    }

    public function isSimpleMode(): bool
    {
        return $this->simpleMode;
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->beforeLabel();
        $this->wrapperClass('form-group-inline');

        if ($this->isSimpleMode()) {
            return;
        }

        $this->customAttributes([
            'x-bind:checked' => '$el.checked',
        ]);

        $this->mergeAttribute('x-on:change', $this->getOnChangeEvent(), ';');
    }

    protected function resolveRawValue(): mixed
    {
        return (string) ($this->toValue(false)
            ? $this->onValue
            : $this->offValue);
    }

    protected function resolvePreview(): Renderable|string
    {
        return Boolean::make(
            (bool) parent::resolvePreview()
        )->render();
    }

    protected function getOnChangeEvent(): string
    {
        return '$el.value = $el.checked ? `' . $this->getOnValue() . '` : `' . $this->getOffValue() . '`';
    }

    /**
     * @return array<string, string>
     */
    protected function getOnChangeEventAttributes(?string $url = null): array
    {
        $additionally = [];

        if ($onChange = $this->getAttribute('x-on:change')) {
            $this->removeAttribute('x-on:change');
            $additionally['x-on:change'] = $onChange;
        }

        if ($url) {
            return AlpineJs::onChangeSaveField(
                $url,
                $this->getColumn(),
                $this->getOnChangeEvent(),
                $additionally
            );
        }

        return $additionally;
    }

    protected function resolveOnApply(): ?Closure
    {
        return function ($item) {
            if ($this->getAttribute('disabled')) {
                return $item;
            }

            $value = $this->getOnValue() == $this->getRequestValue() ? $this->getOnValue() : $this->getOffValue();

            if (is_numeric($value)) {
                $value = (int) $value;
            }

            data_set($item, $this->getColumn(), $value);

            return $item;
        };
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'onValue' => $this->getOnValue(),
            'offValue' => $this->getOffValue(),
            'isChecked' => $this->isChecked(),
            'isSimpleMode' => $this->isSimpleMode(),
        ];
    }
}
