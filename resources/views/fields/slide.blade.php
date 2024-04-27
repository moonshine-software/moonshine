@props([
    'value' => [],
    'fromColumn' => 'range_from',
    'toColumn' => 'range_to',
    'fromValue' => '',
    'toValue' => '',
    'fromAttributes' => null,
    'toAttributes' => null,
])
<x-moonshine::form.slide-range
    :attributes="$attributes"
    :fromAttributes="$fromAttributes"
    :toAttributes="$toAttributes"
    :fromValue="$fromValue"
    :toValue="$toValue"
    fromName="{{ $fromColumn }}"
    toName="{{ $toColumn }}"
    fromField="{{ $column }}.{{ $fromField }}"
    toField="{{ $column }}.{{ $toField }}"
/>
