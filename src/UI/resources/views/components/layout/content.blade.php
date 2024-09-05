<main {{ $attributes->class(['layout-content']) }}>
    <x-moonshine::components
        :components="$components"
    />

    {{ $slot ?? '' }}
</main>
