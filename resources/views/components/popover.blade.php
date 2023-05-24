@props([
    'title' => '',
    'placement' => 'right',
    'trigger',
])
<span {{ $attributes }} title="{{ $title }}" data-content="{!! $slot !!}"
     x-data="popover({placement: '{{ $placement }}'})"
>
    {{ $trigger }}
</span>
