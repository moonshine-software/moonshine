@props([
    'content' => '',
    'placement' => 'right',
])
<span {{ $attributes->class(['inline-block']) }}
     x-data="tooltip(@js($content), {placement: @js($placement)})"
>
    {{ $slot ?? '' }}
</span>
