@props([
    'h' => 1
])
<h{{ $h }} {{ $attributes->merge(['class' => 'truncate text-xl font-medium']) }}>
    {{ $slot ?? '' }}
</h{{ $h }}>
