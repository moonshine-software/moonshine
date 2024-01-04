<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\View\View;
use MoonShine\Components\Boolean;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeBool;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasUpdateOnPreview;
use MoonShine\Support\AlpineJs;
use MoonShine\Traits\Fields\BooleanTrait;
use MoonShine\Traits\Fields\UpdateOnPreview;
use MoonShine\Traits\Fields\WithDefaultValue;

class Checkbox extends Field implements
    HasDefaultValue,
    DefaultCanBeNumeric,
    DefaultCanBeString,
    DefaultCanBeBool,
    HasUpdateOnPreview
{
    use BooleanTrait;
    use WithDefaultValue;
    use UpdateOnPreview;

    protected string $view = 'moonshine::fields.checkbox';

    protected string $type = 'checkbox';

    public function isChecked(): bool
    {
        return $this->getOnValue() == $this->value();
    }

    protected function resolveValue(): mixed
    {
        $this->beforeLabel();
        $this->customWrapperAttributes([
            'class' => 'form-group-inline',
        ]);

        $this->customAttributes([
            'x-on:change' => $this->onChangeEvent(),
            'x-bind:checked' => '$el.checked',
        ]);

        return parent::resolveValue();
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
                $this->column(),
                $this->onChangeEvent(),
                $additionally
            );
        }

        return $additionally;
    }
}
