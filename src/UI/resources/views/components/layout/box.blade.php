@props([
    'escapeLabel' => moonshine()->getConfig()->isEscapeLabel(),
    'components' => [],
    'label' => false,
    'dark' => false,
    'icon' => null,
])
<div {{ $attributes->class(['box space-elements', 'box--dark' => $dark]) }}>
    @if($label || $icon->isNotEmpty()) <h2 class="box-title">{{ $icon ?? '' }}{!! $escapeLabel ? e($label ?? '') : ($label ?? '') !!}</h2> @endif

    @foreach($components as $component)
        {!! $component !!}
    @endforeach

    {{ $slot ?? '' }}
</div>
