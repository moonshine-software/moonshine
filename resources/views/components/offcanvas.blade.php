@props([
    'left' => false,
    'title' => '',
    'toggler'
])
<div x-data="offcanvas">
    <button type="button" @click.prevent="toggleCanvas" {{ $toggler->attributes->merge(['class' => 'btn']) }}>
        {{ $toggler }}
    </button>

    <template x-teleport="body">
        <div class="offcanvas-template">
            <div
                x-show="open"
                x-bind="dismissCanvas"
                @if($left)
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-full"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 -translate-x-full"
                @else
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-full"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-full"
                @endif
                class="offcanvas offcanvas-{{ $left ? 'left' : 'right' }}"
                aria-modal="true"
                role="dialog"
            >
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title">{{ $title }}</h5>
                    <button type="button" class="btn btn-close" @click.prevent="toggleCanvas" aria-label="Close">
                        <x-moonshine::icon icon="heroicons.x-mark" />
                    </button>
                </div>
                <div class="offcanvas-body">
                    {{ $slot }}
                </div>
            </div>
            <div x-show="open" x-transition.opacity class="offcanvas-backdrop"></div>
        </div>
    </template>
</div>
