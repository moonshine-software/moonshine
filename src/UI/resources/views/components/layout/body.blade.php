@props([
    'components' => []
])
<body {{ $attributes->merge(['class' => 'antialiased']) }}
    x-cloak
    x-data="{ minimizedMenu: $persist(false).as('minimizedMenu') }"
>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}

    @include('moonshine::shared.img-popup')
    @include('moonshine::shared.toasts')

    @stack('scripts')
</body>
