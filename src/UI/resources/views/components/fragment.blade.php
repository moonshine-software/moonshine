@props([
    'name',
    'interval' => null,
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

@if($interval)
    <script>
        setInterval(() => {
            window.dispatchEvent(new CustomEvent('fragment_updated:{{ $name }}'))
        }, {{ $interval }})
    </script>
@endif
