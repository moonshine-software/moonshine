@props([
    'label',
    'labelRaw' => false,
    'escapeUi' => false,
])
<li {{ $attributes->class('menu-divider') }}>
    @if($label)
        <span>
            @if(! $escapeUi || $labelRaw)
                {!! $label !!}
            @else
                {{ $label }}
            @endif
        </span>
    @endif
</li>
