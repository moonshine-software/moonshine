@props([
    'type' => 'default',
    'content' => '',
    'duration' => null,
    'showOnCreate' => true
])

@if($showOnCreate)
<div x-data
     x-init="$nextTick(() => { $dispatch('toast', {type: @js($type), text: @js($content), duration: @js($duration)}) })"
></div>
@else
    <div x-data="{ show(){$dispatch('toast', {type: @js($type), text: @js($content), duration: @js($duration)})} }">
        {{ $slot ?? '' }}
    </div>
@endif
