@props([
    'escapeLabel' => null,
    'components' => [],
    'label' => false,
    'labelHtml' => null,
    'dark' => false,
    'icon' => null,
])
<div {{ $attributes->class(['box space-elements', 'box--dark' => $dark]) }}>
    @if($label || $icon->isNotEmpty()) <h2 class="box-title">{{ $icon ?? '' }}<x-moonshine::display-text :value="$labelHtml ?? $label" :escape="$escapeLabel" /></h2> @endif

    @foreach($components as $component)
        {!! $component !!}
    @endforeach

    {{ $slot ?? '' }}
</div>
