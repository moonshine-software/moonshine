<span x-data="{isOpen: false}" x-cloak x-init="$refs.cover.classList.remove('hidden')">
    <div x-ref="cover" :class="isOpen == false ? 'hidden' : ''" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>


            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

            <div class="inline-block align-bottom rounded-lg
            text-left overflow-hidden shadow-xl transform transition-all
            sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                 x-show="isOpen" @click.outside="isOpen = false" x-transition:enter="ease-out transition-slow"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in transition-slow" x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
            >
                <div class="bg-white dark:bg-dark px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div @if(isset($icon)) class="sm:flex sm:items-start" @endif>
                        @if(isset($icon))
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                {{ $icon }}
                            </div>
                        @endif

                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            @if(isset($title))
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-headline">
                                    {{ $title }}
                                </h3>
                            @endif

                            @if(isset($slot))
                                <div class="mt-2">
                                    <p class="text-sm leading-5">
                                        {{ $slot }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if(isset($buttons))
                    <div class="bg-gray-50 dark:bg-purple px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        {{ $buttons }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{ $outerHtml ?? '' }}
</span>
