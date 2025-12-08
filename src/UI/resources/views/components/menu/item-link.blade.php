@props([
    'label' => '',
    'previewLabel' => '',
    'url' => 'javascript:void(0);',
    'icon' => '',
    'onlyIcon' => false,
    'badge' => false,
    'top' => false,
    'hasComponent' => false,
    'component' => null,
])
<a
    href="{{ $url }}"
    {{ $attributes?->merge(['class' => 'menu-link']) }}
    @if($onlyIcon && !$attributes->has('x-data'))
        x-data="navTooltip"
        @mouseenter="toggleTooltip()"
    @endif
>
    @if($icon)
        <div class="menu-icon">
            {!! $icon !!}
        </div>
    @elseif($onlyIcon)
        <div class="menu-icon">
            <x-moonshine::icon icon="squares-2x2" />
        </div>
    @endif

    <span class="menu-text @if($onlyIcon) menu-only-icon @endif">{{ $label }}</span>

    @if($badge !== false)
        <span class="menu-badge">{{ $badge }}</span>
    @endif
</a>

@if($hasComponent)
    <template x-teleport="body">
        {!! $component !!}
    </template>
@endif
