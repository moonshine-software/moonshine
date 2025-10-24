@props([
    'label' => '',
    'previewLabel' => '',
    'url' => 'javascript:void(0);',
    'icon' => '',
    'badge' => false,
    'top' => false,
    'hasComponent' => false,
    'component' => null,
])
<a
    href="{{ $url }}"
    {{ $attributes?->merge(['class' => 'menu-link']) }}
>
    @if($icon)
        <div class="menu-icon">
            {!! $icon !!}
        </div>
    @else
        <span class="menu-char">
            {{ $previewLabel }}
        </span>
    @endif

    <span class="menu-text">{{ $label }}</span>

    @if($badge !== false)
        <span class="menu-badge">{{ $badge }}</span>
    @endif
</a>

@if($hasComponent)
    <template x-teleport="body">
        {!! $component !!}
    </template>
@endif
