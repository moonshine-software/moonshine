@props([
    'name',
    'interval' => null,
    'updateOnLoad' => null,
    'withLoader' => false,
    'components' => [],
])
@fragment($name)
    <div {{ $attributes }}>
        @if($withLoader)
            <div data-fragment-content x-bind:inert="busy || null">
        @endif
        <x-moonshine::components
            :components="$components"
        />

        {{ $slot ?? '' }}
        @if($withLoader)
            </div>
            <div x-show="busy" style="display: none" aria-hidden="true"
                 class="absolute inset-0 z-10 grid place-items-center bg-base/70">
                <x-moonshine::spinner color="primary" size="lg" />
            </div>
        @endif
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
