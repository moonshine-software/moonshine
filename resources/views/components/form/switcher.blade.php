@props([
    'onValue' => '1',
    'offValue' => '0'
])
<div class="form-switcher" x-data>
    <x-moonshine::form.input
        :attributes="$attributes->merge(['class' => 'peer sr-only'])"
        type="checkbox"
        ::value="$el.checked ? '{{ $onValue }}' : '{{ $offValue }}'"
    />

    <span class="form-switcher-toggler peer"></span>
</div>
