@props([
    'content' => '',
    'placement' => 'right',
])
<span {{ $attributes->class(['inline-block']) }}
     x-data="tooltip(`{{ $content }}`, {placement: '{{ $placement }}'})"
>
    {{ $slot ?? '' }}
</span>
