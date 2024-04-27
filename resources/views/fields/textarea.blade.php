@props([
    'value' => '',
    'label' => '',
])
<x-moonshine::form.textarea
    :attributes="$attributes->merge([
        'aria-label' => $label ?? '',
    ])"
>{!! $value ?? '' !!}</x-moonshine::form.textarea>
