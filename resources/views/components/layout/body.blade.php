@props([
    'components' => []
])
<body {{ $attributes->merge(['class' => 'antialiased']) }}
    x-cloak
    x-data="{ minimizedMenu: $persist(false).as('minimizedMenu'), asideMenuOpen: false }"
>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}

    @include('moonshine::layouts.shared.img-popup')
    @include('moonshine::layouts.shared.toasts')

    @stack('scripts')
</body>
