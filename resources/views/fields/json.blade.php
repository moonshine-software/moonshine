@props([
    'component'
])
<div x-id="['json']"
     :id="$id('json')"
     {{ $attributes->only('class') }}
     data-show-when-field="{{ $attributes->get('name') }}"
>
    {!! $component !!}
</div>
