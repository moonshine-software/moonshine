@if(count($filters))
    <div x-data="{ filtersOpen: false }">
        <button @click.stop="filtersOpen = ! filtersOpen"
                class="ml-3 bg-purple rounded-full p-2 focus:outline-none
                    transition
                    duration-500 ease-in-out">

            @include('moonshine::shared.icons.filter', ['size' => 5, 'color' => 'white'])
        </button>

        <div x-show="filtersOpen" class="fixed inset-0 flex z-40" role="dialog" aria-modal="true">
            <div x-show="filtersOpen" x-bind:class="!filtersOpen ? 'opacity-0' : 'opacity-100'" x-transition
                 class="fixed inset-0 bg-black bg-opacity-25 transition-opacity ease-linear duration-300"
                 aria-hidden="true"></div>

            <div x-show="filtersOpen"
                 @click.outside="filtersOpen = false"
                 x-bind:class="!filtersOpen ? 'translate-x-full' : 'translate-x-0'"
                 class="transition ease-in-out duration-300 transform ml-auto relative
                 max-w-xs w-full h-full bg-white dark:bg-dark shadow-xl py-4 pb-12

                 flex flex-col overflow-y-auto"
            >
                <div class="px-4 flex items-center justify-between">
                    <h2 class="text-lg font-medium">@lang('moonshine::ui.filters')</h2>

                    <button @click="filtersOpen = false" type="button"
                            class="-mr-2 w-10 h-10 bg-white text-black dark:bg-purple dark:text-white p-2 rounded-md flex items-center justify-center">
                        <span class="sr-only">@lang('moonshine::ui.close')</span>

                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form class="w-full max-w-sm p-5" action="{{ $resource->route("index") }}" method="get">
                    @csrf

                    @foreach($filters as $filter)
                        @if($filter->isSee($resource->getModel()))
                            <div class="mb-4">
                                <div>
                                    <x-moonshine::filter-container :filter="$filter" :resource="$resource">
                                        {{ $resource->renderFilter($filter, $resource->getModel()) }}
                                    </x-moonshine::filter-container>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <div class="mt-5">
                        <button type="submit" class="bg-transparent hover:bg-purple text-purple
                        font-semibold hover:text-white py-2 px-4 border border-purple
                        hover:border-transparent rounded">
                            {{ trans('moonshine::ui.search') }}
                        </button>
                    </div>

                    @if(request('filters'))
                        <div class="mt-5">
                            <a href="{{ $resource->route('index', query: ['reset' => true]) }}"
                               class="bg-transparent hover:bg-purple text-purple
                        font-semibold hover:text-white py-2 px-4 border border-purple
                        hover:border-transparent rounded">
                                {{ trans('moonshine::ui.reset') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

@endif
