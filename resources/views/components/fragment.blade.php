@props([
    'name',
    'components' => [],
])
@fragment($name)
    <div {{ $attributes }}>
        <x-moonshine::components
            :components="$components"
        />

        {{ $slot ?? '' }}
    </div>
@endfragment
