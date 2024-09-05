@props([
    'components' => [],
    'label' => false,
    'icon' => false,
    'dark' => false
])
<div {{ $attributes->class(['box', 'box-dark' => $dark]) }}>
    @if($icon || $label) <h2 class="box-title">{{ $icon ?? '' }}{{ $label ?? '' }}</h2> @endif

    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
