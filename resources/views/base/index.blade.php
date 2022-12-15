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
    @include("moonshine::shared.title", ["title" => $resource->title()])
    @include("moonshine::shared.sub-title", ["subTitle" => $resource->subTitle()])
@endsection


@section('content')
    <div class="mt-1 mb-4 flex flex-wrap items-center justify-between">
        @if($resource->search())
            <div class="flex items-center select-none mt-5">
                @include("moonshine::base.index.shared.search")
            </div>
        @endif

        @if($actions)
            <div class="flex items-center select-none mt-5">
                @include("moonshine::base.index.shared.actions", [
                    'actions' => $actions
                ])
            </div>
        @endif
    </div>


    @if(count($metrics))
        <div class="mt-8"></div>

        <div class="flex items-center justify-between my-8 space-x-4 space-y-4">
            @foreach($metrics as $metric)
                {!! $resource->renderMetric($metric) !!}
            @endforeach
        </div>
    @endif


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

    <div class="mt-1 mb-4 flex items-center justify-between">
        <span class="text-sm">
            {{ trans('moonshine::ui.total') }}
            <strong>{{ $items->total() }}</strong>
        </span>
    </div>

    <div class="flex flex-col mt-8">
        <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
            <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg">
                <table class="min-w-full" x-data="actionBarHandler()"
                       x-init="actionBar('main'); $refs.foot.classList.remove('hidden')">
                    <thead class="bg-whiteblue dark:bg-purple">
                    @include("moonshine::base.index.head", [$resource])
                    </thead>

                    <tbody class="bg-white dark:bg-darkblue text-black dark:text-white">
                    @include("moonshine::base.index.items", [$resource, $items])
                    </tbody>

                    <tfoot x-ref="foot"
                           class="hidden bg-whiteblue dark:bg-purple"
                           :class="actionBarOpen ? 'translate-y-0 ease-out' : '-translate-y-full ease-in hidden'">

                    @include("moonshine::base.index.foot", [$resource])
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-5">
        {{ $items->links('moonshine::shared.pagination') }}
    </div>
@endsection
