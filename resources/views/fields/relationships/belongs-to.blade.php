@props([
    'value' => '',
    'values' => [],
    'customProperties' => [],
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
    :customProperties="$customProperties"
    :asyncRoute="$isAsyncSearch ? $asyncSearchUrl : null"
>
</x-moonshine::form.select>
