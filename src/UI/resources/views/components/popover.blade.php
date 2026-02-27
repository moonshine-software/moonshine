@props([
    'title' => '',
    'placement' => 'right',
    'trigger',
    'triggerRaw' => false,
])
<span
    {{ $attributes }}
    class="popover-trigger"
    title="{{ $title }}"
    x-data="popover({placement: '{{ $placement }}'})"
>
    @if($triggerRaw)
        {!! $trigger !!}
    @else
        {{ $trigger }}
    @endif
    <div class="hidden popover-body-content">{!! $slot !!}</div>
</span>
