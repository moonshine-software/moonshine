@props([
    'value' => [],
    'fromColumn' => 'range_from',
    'toColumn' => 'range_to',
    'fromValue' => '',
    'toValue' => '',
    'fromAttributes' => null,
    'toAttributes' => null,
])
<div
    x-data="{
        [@js($fromColumn)]: @js($fromValue ?? ''),
        [@js($toColumn)]: @js($toValue ?? ''),
    }"
    data-range-from-column="{{ $fromColumn }}"
    data-range-to-column="{{ $toColumn }}"
    {{ $attributes
        ->only('class')
        ->merge(['class' => 'form-group form-group-inline']) }}

    data-show-when-field="{{ $attributes->get('data-show-when-field', $attributes->get('name')) }}"
>
    <x-moonshine::form.input
        :attributes="$fromAttributes"
        x-bind:max="$data[$root.dataset.rangeToColumn]"
        x-model="$data[$root.dataset.rangeFromColumn]"
        value="{{ $fromValue ?? '' }}"
    />

    <x-moonshine::form.input
        :attributes="$toAttributes"
        x-bind:min="$data[$root.dataset.rangeFromColumn]"
        x-model="$data[$root.dataset.rangeToColumn]"
        value="{{ $toValue ?? '' }}"
    />
</div>
