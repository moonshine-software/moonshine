@props([
    'components' => [],
    'title' => false,
    'dark' => false
])
<div {{ $attributes->class(['box', 'box-dark' => $dark]) }}>
    @if($title ?? false) <h2 class="box-title">{{ $title }}</h2> @endif

    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
