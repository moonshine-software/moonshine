@props([
    'onValue' => '1',
    'offValue' => '0'
])
<label {{ $attributes->class(['form-switcher'])->only('class') }} x-data>
    <x-moonshine::form.input
        type="hidden"
        :attributes="$attributes->only(['data-name', 'data-level'])"
        :name="$attributes->get('name')"
        value="{{ $offValue }}"
    />

    <x-moonshine::form.input
        :attributes="$attributes->merge(['class' => 'peer sr-only'])"
        type="checkbox"
        x-bind:checked="$el.checked"
        x-on:change="$el.checked ? $el.value = '{{ $onValue }}' : $el.value = '{{ $offValue }}'"
    />

    <span class="form-switcher-toggler peer"></span>
</label>
