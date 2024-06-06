@props([
    'title' => '',
    'placement' => 'right',
    'trigger',
])
<span
    {{ $attributes }}
    title="{{ $title }}"
    x-data="popover({placement: '{{ $placement }}'})"
>
    {!! $trigger !!}

    <div class="hidden popover-content">
        {!! $slot !!}
    </div>
</span>
