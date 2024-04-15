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

@include('moonshine::ui.img-popup')
@include('moonshine::ui.toasts')

@stack('scripts')
</body>
