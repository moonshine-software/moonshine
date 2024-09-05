@props([
    'value' => true
])
<div {{ $attributes->class(['h-2 w-2 rounded-full', 'bg-green-500' => $value, 'bg-red-500' => !$value]) }}></div>
