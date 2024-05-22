@props([
    'value' => '',
    'values' => [],
    'isNullable' => false,
    'isSearchable' => false,
    'isAsyncSearch' => false,
    'asyncSearchUrl' => '',
])
<x-moonshine::form.select
    :attributes="$attributes"
    :nullable="$isNullable"
    :searchable="$isSearchable"
    :value="$value"
    :values="$values"
    :asyncRoute="$isAsyncSearch ? $asyncSearchUrl : null"
>
</x-moonshine::form.select>
