@props([
    'value' => '',
    'extensions' => [],
])
<x-moonshine::form.input-extensions
    :extensions="$extensions"
>
    <x-moonshine::form.input
        :attributes="$attributes->merge([
            'value' => $value
        ])"
    />
</x-moonshine::form.input-extensions>
