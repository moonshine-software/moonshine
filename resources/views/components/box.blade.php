@props([
    'title' => false,
    'icon' => false,
    'dark' => false
])
<div {{ $attributes->class(['box', 'box-dark' => $dark]) }}>
    @if(($title ?? false) || ($icon ?? false))
        <h2 class="box-title">
            @if($icon ?? false)
                <x-moonshine::icon :icon="$icon" />
            @endif

            {{ $title }}
        </h2>
    @endif

    {{ $slot }}
</div>
