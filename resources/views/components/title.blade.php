@props([
    'h' => 1
])
<h{{ $h }} {{ $attributes->merge(['class' => 'truncate text-md font-medium']) }}>
    {{ $slot ?? '' }}
</h{{ $h }}>
