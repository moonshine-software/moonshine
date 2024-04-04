@props([
    'components' => [],
    'bodyClass' => '',
])
<body
    class="antialiased {{ $bodyClass ?? '' }}"
    x-cloak
    x-data="{ minimizedMenu: $persist(false).as('minimizedMenu'), asideMenuOpen: false }"
>
<div {{ $attributes->merge(['class' => 'layout-wrapper']) }}
     :class="minimizedMenu && 'layout-wrapper-short'"
     x-data="interactsWithAsync"
>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</div>
@include('moonshine::ui.img-popup')
@include('moonshine::ui.toasts')

@stack('scripts')
</body>
