<div x-data="{ open : false, src : ''}">
    <template
        @img-popup.window="open = true; src = $event.detail.src;"
        x-if="open"
        x-teleport="body"
    >
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
                @click.self="open=false"
            >
                <div class="modal-dialog modal-dialog-auto" x-bind="dismissModal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button"
                                    class="btn btn-close"
                                    @click.stop="open=false"
                                    aria-label="Close"
                            >
                                <x-moonshine::icon
                                    icon="heroicons.x-mark"
                                    size="6"
                                />
                            </button>
                        </div>
                        <div class="modal-body">
                            <img @click.outside="open = false"
                                 src=""
                                 :src="src"
                                 alt=""
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div x-show="open" x-transition.opacity class="modal-backdrop"></div>
        </div>
    </template>
</div>
