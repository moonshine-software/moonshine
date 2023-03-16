@props([
    'wide' => false,
    'title' => '',
    'outerHtml' => ''
])
<div x-data="modal">
    <div class="modal-template">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-10"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-10"
            class="modal"
            aria-modal="true"
            role="dialog"
        >
            <div class="modal-dialog @if($wide) modal-dialog-xl @endif" x-bind="dismissModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $title ?? '' }}</h5>
                        <button type="button" class="btn btn-close" @click.prevent="toggleModal" aria-label="Close">
                            <x-moonshine::icon
                                icon="heroicons.x-mark"
                                size="6"
                            />
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ $slot ?? '' }}
                    </div>
                </div>
            </div>
        </div>
        <div x-show="open" x-transition.opacity class="modal-backdrop"></div>
    </div>

    {{ $outerHtml ?? '' }}
</div>
