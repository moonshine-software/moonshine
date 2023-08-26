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
    :asyncRoute="$element->isAsyncSearch() && $element->isNowOnForm() ?
    moonshineRequest()->getResource()->route(
        'relation.search',
        moonshineRequest()->getResource()->getItemID(),
        query: ['_relation' => $element->getRelationName()]
    ) : null"
>
</x-moonshine::form.select>
