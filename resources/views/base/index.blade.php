@extends("moonshine::layouts.app")

@section('sidebar-inner')
    @parent
@endsection

@section('header-inner')
    @parent

    @include("moonshine::shared.title", ["title" => $resource->title()])
    @include("moonshine::shared.sub-title", ["subTitle" => $resource->subTitle()])
@endsection


@section('content')
    @if(!$resource->isRelatable())
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
    @endif

    @if($resource->can('create') && in_array('create', $resource->getActiveActions()))
        @if($resource->isCreateInModal())
            <x-moonshine::async-modal id="create_modal" route="{{ $resource->route('create', query: request('related_column') ? ['related_column' => request('related_column'), 'related_key' => request('related_key')] : []) }}" class="inline-flex  items-center bg-transparent hover:bg-purple text-purple border border-purple hover:text-white hover:border-transparent font-semibold  py-2 px-4 rounded">
                @include("moonshine::shared.icons.add", ["size" => 4, "class" => "mr-2"])
                <span>{{ trans('moonshine::ui.create') }}</span>
            </x-moonshine::async-modal>
        @else
            @include('moonshine::shared.btn', [
                'title' => trans('moonshine::ui.create'),
                'href' => $resource->route("create"),
                'icon' => 'add',
                'filled' => false,
            ])
        @endif
    @endif

    @if(!empty($resource->queryTags()))
        <div class="mt-8"></div>

        <div class="flex items-center justify-start space-x-2">
            @foreach($resource->queryTags() as $queryTag)
                @include('moonshine::shared.btn', [
                    'href' => $resource->route("query-tag", query: ['uri' => $queryTag->uri()]),
                    'filled' => request()->routeIs('*.query-tag') && request()->route('uri') === $queryTag->uri(),
                    'title' => $queryTag->label(),
                    'icon' => $queryTag->iconValue()
                ])
            @endforeach
        </div>
    @endif

    @if(!$resource->isRelatable())
        <div class="mt-8"></div>

        <div class="mt-1 mb-4 flex items-center justify-between">
            <span class="text-sm">
                {{ trans('moonshine::ui.total') }}
                <strong>{{ $items->total() }}</strong>
            </span>
        </div>
    @endif

    @include('moonshine::base.index.table', [
        'resource' => $resource,
        'items' => $items,
    ])

    @if(!$resource->isRelatable())
        <div class="mt-5">
            {{ $items->links('moonshine::shared.pagination') }}
        </div>
    @endif
@endsection
