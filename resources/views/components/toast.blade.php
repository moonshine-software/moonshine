@props([
    'type' => 'default',
    'content' => '',
    'showOnCreate' => true
])

@if($showOnCreate)
<div x-data
     x-init="$nextTick(() => { $dispatch('toast', {type: '{{ $type }}', text: '{{ $content }}'}) })"
></div>
@else
    <div x-data="{ show(){$dispatch('toast', {type: '{{ $type }}', text: '{{ $content }}'})} }">
        {{ $slot }}
    </div>
@endif

