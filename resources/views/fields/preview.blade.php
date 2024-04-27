@props([
    'value' => ''
])
<div {{ $attributes }}>
    {!! $value ?? '' !!}
</div>
