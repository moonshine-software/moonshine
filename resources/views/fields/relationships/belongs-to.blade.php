<x-moonshine::form.select
    :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'name' => $element->name(),
    ])"
    :nullable="$element->isNullable()"
    :searchable="$element->isSearchable()"
    @class(['form-invalid' => $errors->{$element->getFormName()}->has($element->name())])
    :value="$element->value()"
    :values="$element->values()"
    :customProperties="$element->valuesWithProperties(onlyCustom: true)"
    :asyncRoute="$element->isAsyncSearch() ? $element->asyncSearchUrl($element->getFormName()) : null"
>
</x-moonshine::form.select>
