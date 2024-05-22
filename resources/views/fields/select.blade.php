@props([
    'value' => '',
    'values' => [],
    'isNullable' => false,
    'isSearchable' => false,
    'asyncUrl' => '',
])
<x-moonshine::form.select
        :attributes="$attributes"
        :values="$values"
        :nullable="$isNullable"
        :searchable="$isSearchable"
        :asyncRoute="$asyncUrl"
/>
