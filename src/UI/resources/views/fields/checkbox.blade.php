@props([
    'onValue' => '',
    'offValue' => '',
    'value' => '',
    'isChecked' => false,
    'isSimpleMode' => false,
])
<div @if(!$isSimpleMode) x-data @endif>
    @if(!$isSimpleMode)
        <x-moonshine::form.input
            type="hidden"
            :attributes="$attributes->except(['class', ':id', 'id', 'type', 'checked', 'value', 'x-model'])->merge(['data-checkbox-hidden' => true])"
            value="{{ $offValue }}"
        />
    @endif

    <x-moonshine::form.input
        :attributes="$attributes->merge([
            'value' => $isSimpleMode ? $value : $onValue,
            'checked' => $isChecked
        ])"
    />
</div>
