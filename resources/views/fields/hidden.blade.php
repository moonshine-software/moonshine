@props([
    'value' => '',
])
<x-moonshine::form.input
    :attributes="$attributes->merge([
        'value' => (string) $value
    ])"
/>
