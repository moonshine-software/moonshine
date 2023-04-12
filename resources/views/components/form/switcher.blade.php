@props([
    'onValue' => '1',
    'offValue' => '0'
])
<label class="form-switcher" x-data>
    <x-moonshine::form.input
        type="hidden"
        :name="$attributes->get('name')"
        :attributes="$attributes->only(['x-bind:name'])"
        value="{{ $offValue }}"
    />

    <x-moonshine::form.input
        :attributes="$attributes->merge(['class' => 'peer sr-only'])"
        type="checkbox"
        x-bind:checked="{{ $attributes->get('x-model') ? $attributes->get('x-model') . '==`'.$onValue.'`' : '$el.checked' }}"
        x-on:change="$el.checked ? $el.value = '{{ $onValue }}' : $el.value = '{{ $offValue }}'"
    />

    <span class="form-switcher-toggler peer"></span>
</label>
