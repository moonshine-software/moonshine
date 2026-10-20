@props([
    'name',
    'interval' => null,
    'updateOnLoad' => null,
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

@if($updateOnLoad)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent("fragment_updated:" + @js($name)))
        });
    </script>
@endif

@if($interval)
    <script>
        setInterval(() => {
            window.dispatchEvent(new CustomEvent("fragment_updated:" + @js($name)))
        }, @js($interval))
    </script>
@endif
