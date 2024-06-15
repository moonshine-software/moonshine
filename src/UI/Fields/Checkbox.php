<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\Support\AlpineJs;
use MoonShine\UI\Components\Boolean;
use MoonShine\UI\Contracts\Fields\DefaultValueTypes\DefaultCanBeBool;
use MoonShine\UI\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\UI\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\UI\Contracts\Fields\HasDefaultValue;
use MoonShine\UI\Contracts\Fields\HasReactivity;
use MoonShine\UI\Contracts\Fields\HasUpdateOnPreview;
use MoonShine\UI\Traits\Fields\BooleanTrait;
use MoonShine\UI\Traits\Fields\Reactivity;
use MoonShine\UI\Traits\Fields\UpdateOnPreview;
use MoonShine\UI\Traits\Fields\WithDefaultValue;

class Checkbox extends Field implements
    HasDefaultValue,
    DefaultCanBeNumeric,
    DefaultCanBeString,
    DefaultCanBeBool,
    HasUpdateOnPreview,
    HasReactivity
{
    use BooleanTrait;
    use WithDefaultValue;
    use UpdateOnPreview;
    use Reactivity;

    protected string $view = 'moonshine::fields.checkbox';

    protected string $type = 'checkbox';

    protected bool $simpleMode = false;

    public function isChecked(): bool
    {
        if($this->isSimpleMode()) {
            return false;
        }

        return $this->getOnValue() == $this->value();
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

        if($this->isSimpleMode()) {
            return;
        }

        $this->beforeLabel();
        $this->customWrapperAttributes([
            'class' => 'form-group-inline',
        ]);

        $this->customAttributes([
            'x-bind:checked' => '$el.checked',
        ]);

        $this->mergeAttribute('x-on:change', $this->onChangeEvent(), ';');
    }

    protected function resolvePreview(): View|string
    {
        if ($this->isRawMode()) {
            return (string) ($this->toValue(false)
                ? $this->onValue
                : $this->offValue);
        }

        return Boolean::make(
            (bool) parent::resolvePreview()
        )->render();
    }

    protected function onChangeEvent(): string
    {
        return '$el.value = $el.checked ? `' . $this->getOnValue() . '` : `' . $this->getOffValue() . '`';
    }

    protected function onChangeEventAttributes(?string $url = null): array
    {
        $additionally = [];

        if($onChange = $this->attributes()->get('x-on:change')) {
            $this->removeAttribute('x-on:change');
            $additionally['x-on:change'] = $onChange;
        }

        if($url) {
            return AlpineJs::requestWithFieldValue(
                $url,
                $this->getColumn(),
                $this->onChangeEvent(),
                $additionally
            );
        }

        return $additionally;
    }

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
