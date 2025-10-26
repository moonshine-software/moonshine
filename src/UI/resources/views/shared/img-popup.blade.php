<div x-data="{ open : false, src : '', auto: true, wide: false, styles: ''}">
    <template
        @img-popup.window="open = true; src = $event.detail.src; auto = $event.detail.auto; wide = $event.detail.wide; styles = $event.detail.styles"
        x-if="open"
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
                <div 
                    class="modal-dialog"
                    :class="{'modal-dialog-auto': auto, 'modal-dialog-xl': wide}"
                >
                    <div class="modal-content">
                        <div class="modal-header">
                            <button 
                                type="button"
                                class="modal-close btn-fit"
                                @click.stop="open=false"
                                aria-label="Close"
                            >
                                <x-moonshine::icon icon="x-mark" />
                            </button>
                        </div>
                        <div class="modal-body">
                            <img 
                                @click.outside="open = false"
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
