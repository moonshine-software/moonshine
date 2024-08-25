@props([
    'components' => [],
    'label' => false,
    'dark' => false
])
<div {{ $attributes->class(['box', 'box-dark' => $dark]) }}>
    @if($label ?? false) <h2 class="box-title">{{ $label }}</h2> @endif

    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
