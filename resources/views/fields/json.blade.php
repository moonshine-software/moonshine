@props([
    'component'
])
<div x-id="['json']"
     :id="$id('json')"
     {{ $attributes->only('class') }}
     data-field-block="{{ $attributes->get('name') }}"
>
    {!! $component !!}
</div>
