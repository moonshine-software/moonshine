@props([
    'title' => '',
    'placement' => 'right',
    'trigger',
])
<span
    {{ $attributes }}
    class="popover-trigger"
    title="{{ $title }}"
    x-data="popover({placement: '{{ $placement }}'})"
>
    {!! $trigger !!}
    <div class="hidden popover-body-content">{!! $slot !!}</div>
</span>
