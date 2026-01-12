<div x-data="imgPopup()" @img-popup.window="show($event.detail)">
    <template x-teleport="body">
        <div class="modal-template" data-img-popup>
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="modal"
                aria-modal="true"
                role="dialog"
                @click.self="close"
            >
                <div
                    class="modal-dialog"
                    :class="{'modal-dialog-auto': auto, 'modal-dialog-xl': wide}"
                >
                    <div class="modal-content">
                        <div class="modal-header">
                            <button
                                type="button"
                                class="modal-close btn-fit"
                                @click.stop="close"
                                aria-label="Close"
                            >
                                <x-moonshine::icon icon="x-mark" />
                            </button>
                        </div>
                        <div class="modal-body">
                            <img
                                @click.outside="close"
                                src=""
                                :src="src"
                                :style="styles"
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
