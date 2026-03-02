@props([
    'title' => '',
    'placement' => 'right',
    'trigger',
    'escapeUi' => false,
    'triggerRaw' => false,
])
<span
    {{ $attributes }}
    class="popover-trigger"
    title="{{ $title }}"
    x-data="popover({placement: '{{ $placement }}'})"
>
    @if(! $escapeUi || $triggerRaw)
        {!! $trigger !!}
    @else
        {!! e((string) $trigger) !!}
    @endif

    <div class="hidden popover-body-content">
        @if(! $escapeUi)
            {!! $slot !!}
        @else
            {!! e((string) $slot) !!}
        @endif
    </div>
</span>
