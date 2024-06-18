@props([
    'value' => '',
    'extensions' => [],
    'extensionsAttributes' => null,
])
<x-moonshine::form.input-extensions
    :extensions="$extensions"
    :attributes="$extensionsAttributes"
>
    <x-moonshine::form.input
        :attributes="$attributes->merge([
            'value' => $value
        ])"
    />
</x-moonshine::form.input-extensions>
