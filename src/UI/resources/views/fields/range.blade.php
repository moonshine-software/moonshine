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
         {{ $fromColumn }}: '{{ $fromValue ?? '' }}',
         {{ $toColumn }}: '{{ $toValue ?? '' }}'
     }"
    {{ $attributes
        ->only('class')
        ->merge(['class' => 'form-group form-group-inline']) }}

    data-show-when-field="{{ $attributes->get('name') }}"
>
    <x-moonshine::form.input
        :attributes="$fromAttributes"
        x-bind:max="{{ $toColumn }}"
        x-model="{{ $fromColumn }}"
        value="{{ $fromValue ?? '' }}"
    />

    <x-moonshine::form.input
        :attributes="$toAttributes"
        x-bind:min="{{ $fromColumn }}"
        x-model="{{ $toColumn }}"
        value="{{ $toValue ?? '' }}"
    />
</div>
