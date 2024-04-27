@props([
    'onValue' => '',
    'offValue' => '',
    'value' => '',
    'isChecked' => false,
])
<div x-data>
    <x-moonshine::form.input
        type="hidden"
        :attributes="$attributes->except(['class', 'id', 'type', 'checked', 'value'])"
        value="{{ $offValue }}"
    />

    <x-moonshine::form.input
        :attributes="$attributes->merge([
            'value' => $onValue,
            'checked' => $isChecked
        ])"
    />
</div>
