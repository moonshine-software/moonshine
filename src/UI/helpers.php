<?php

declare(strict_types=1);

use MoonShine\Core\Paginator\PaginatorContract;
use MoonShine\Support\Enums\FormMethod;
use MoonShine\UI\Applies\AppliesRegister;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Contracts\Collections\FieldsCollection;

if (! function_exists('appliesRegister')) {
    function appliesRegister(): AppliesRegister
    {
        return moonshine()->getContainer(AppliesRegister::class);
    }
}

if (! function_exists('fieldsCollection')) {
    /**
     * @template-covariant T of FieldsCollection
     * @param  class-string<T>  $default
     * @return T|Fields
     */
    function fieldsCollection(array $items = [], string $default = Fields::class): FieldsCollection
    {
        return moonshine()
            ->getContainer(FieldsCollection::class, null, items: $items) ?? $default::make($items);
    }
}

if (! function_exists('form')) {
    function form(
        string $action = '',
        FormMethod $method = FormMethod::POST,
        Fields|array $fields = [],
        array $values = []
    ): FormBuilder {
        return FormBuilder::make($action, $method, $fields, $values);
    }
}

if (! function_exists('table')) {
    function table(
        Fields|array $fields = [],
        iterable $items = [],
    ): TableBuilder {
        return TableBuilder::make($fields, $items);
    }
}

if (! function_exists('actionBtn')) {
    function actionBtn(
        Closure|string $label,
        Closure|string $url = '',
        mixed $item = null
    ): ActionButton {
        return ActionButton::make($label, $url, $item);
    }
}
