<div class="flex flex-col mt-8">
    <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full"
                   @if(!$resource->isPreviewMode())
                   x-data="actionBarHandler()"
                   x-init="actionBar('main'); $refs.foot.classList.remove('hidden')"
                   @endif
            >
                <thead class="bg-whiteblue dark:bg-purple">
                    @include("moonshine::base.index.head", [$resource])
                </thead>

                <tbody class="bg-white dark:bg-darkblue text-black dark:text-white">
                    @include("moonshine::base.index.items", [$resource, $items])
                </tbody>

                @if(!$resource->isPreviewMode())
                    <tfoot x-ref="foot"
                           class="hidden bg-whiteblue dark:bg-purple"
                           :class="actionBarOpen ? 'translate-y-none ease-out' : '-translate-y-full ease-in hidden'"
                    >
                        @include("moonshine::base.index.foot", [$resource])
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
