@props([
    'value' => '',
    'isShowValue' => false,
])
@if($isShowValue)
    {{ $value }}
@endif
<x-moonshine::form.input
    :attributes="$attributes->merge([
        'value' => (string) $value
    ])"
/>
