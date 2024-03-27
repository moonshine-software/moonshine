<main {{ $attributes->class(['layout-content']) }}>
    @yield('content')

    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</main>
