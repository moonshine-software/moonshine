@extends("moonshine::layouts.app")

@section('sidebar-inner')
    @parent

    @if($resource->can('create') && in_array('create', $resource->getActiveActions()))
        <div class="text-center mt-8">
            @include('moonshine::shared.btn', [
                'title' => trans('moonshine::ui.create'),
                'href' => $resource->route("create"),
                'filled' => true,
                'icon' => false,
            ])
        </div>
    @endif
@endsection

@section('header-inner')
    @parent
@endsection


@section('content')
    @include("moonshine::shared.title", ["title" => $resource->title()])

    <div class="mt-1 mb-4 flex items-center justify-between">
        <span class="text-sm">
            {{ trans('moonshine::ui.total') }}
            <strong>{{ $resource->paginate()->total() }}</strong>
        </span>
    </div>


    <div class="mt-1 mb-4 flex flex-wrap items-center justify-between">
        @if($resource->search())
            <div class="flex items-center select-none mt-5">
                @include("moonshine::base.index.shared.search")
            </div>

        @endif

        @if($resource->actions())
            <div class="flex items-center select-none mt-5">
                @include("moonshine::base.index.shared.actions", [
                    'actions' => $resource->getActions()
                ])
            </div>
        @endif

        @if($resource->filters())
            <div class="flex items-center select-none mt-5">
                @include("moonshine::base.index.shared.filters")
            </div>
        @endif
    </div>

    <div class="mt-8"></div>

    @if($resource->can('create') && in_array('create', $resource->getActiveActions()))
        @include('moonshine::shared.btn', [
            'title' => trans('moonshine::ui.create'),
            'href' => $resource->route("create"),
            'icon' => 'add',
            'filled' => false,
        ])
    @endif

    <div class="mt-8"></div>

    <div class="flex flex-col mt-8">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                <table class="min-w-full" x-data="actionBarHandler()" x-init="actionBar('main'); $refs.foot.classList.remove('hidden')">
                    <thead>
                        @include("moonshine::base.index.head", [$resource])
                    </thead>

                    <tbody class="bg-white">
                        @include("moonshine::base.index.items", [$resource])
                    </tbody>

                    <tfoot x-ref="foot" :class="actionBarOpen ? 'translate-y-0 ease-out' : '-translate-y-full ease-in hidden'" class="hidden">
                        @include("moonshine::base.index.foot", [$resource])
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-5">
        {{ $resource->paginate()->links() }}
    </div>
@endsection