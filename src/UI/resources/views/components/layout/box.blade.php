@props([
    'components' => [],
    'label' => false,
    'dark' => false,
    'icon' => null,
])
<div {{ $attributes->class(['box space-elements', 'box--dark' => $dark]) }}>
    @if($label || $icon->isNotEmpty()) <h2 class="box-title">{{ $icon ?? '' }}{{ $label ?? '' }}</h2> @endif

    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
