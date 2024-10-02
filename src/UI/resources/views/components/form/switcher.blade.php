@props([
    'onValue' => '1',
    'offValue' => '0'
])
<label {{ $attributes->class(['form-switcher'])->only('class') }} x-data>
    <x-moonshine::form.input
        type="hidden"
        :attributes="$attributes->only(['data-name', 'data-level', 'disabled'])"
        :name="$attributes->get('name')"
        value="{{ $offValue }}"
    />

    <x-moonshine::form.input
        :attributes="$attributes->merge(['class' => 'peer sr-only'])"
        type="checkbox"
    />

    <span class="form-switcher-toggler"></span>
</label>
