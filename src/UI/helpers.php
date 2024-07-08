<?php

declare(strict_types=1);

use MoonShine\UI\Applies\AppliesRegister;
use MoonShine\UI\Collections\Fields;
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
