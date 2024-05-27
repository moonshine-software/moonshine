<?php

declare(strict_types=1);

namespace MoonShine\UI\Fields;

use Illuminate\View\View;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeBool;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeNumeric;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasReactivity;
use MoonShine\Contracts\Fields\HasUpdateOnPreview;
use MoonShine\Support\AlpineJs;
use MoonShine\UI\Components\Boolean;
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

    public function isChecked(): bool
    {
        return $this->getOnValue() == $this->value();
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

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
        ];
    }
}
