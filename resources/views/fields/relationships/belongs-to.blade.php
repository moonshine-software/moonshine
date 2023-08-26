<x-moonshine::form.select
    :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'placeholder' => $element->label() ?? '',
        'name' => $element->name(),
    ])"
    :nullable="$element->isNullable()"
    :searchable="$element->isSearchable()"
    @class(['form-invalid' => $errors->{$element->getFormName()}->has($element->name())])
    :value="$element->value()"
    :values="$element->values()"
    :asyncRoute="$element->isAsyncSearch() ? $element->asyncSearchUrl($element->getFormName()) : null"
>
</x-moonshine::form.select>
