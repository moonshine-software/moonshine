@extends("moonshine::layouts.app")

@section('sidebar-inner')
    @parent
@endsection

@section('header-inner')
    @parent

    @include('moonshine::layouts.shared.breadcrumbs', [
        'items' => [
            $resource->route('index') => $resource->title(),
            '#' => $item->{$resource->titleField()} ?? $item->getKey() ?? trans('moonshine::ui.create')
        ]
    ])
@endsection

@section('content')
    @if(!$resource->isRelatable())
        @if(count($metrics))
            <div class="pb-10">
                <div class="flex flex-col gap-y-8 gap-x-6 sm:grid sm:grid-cols-12 lg:gap-y-10">
                    @foreach($metrics as $metric)
                        {!! $resource->renderComponent($metric, $resource->getModel()) !!}
                    @endforeach
                </div>
            </div>
        @endif
    @endif
    @fragment('crud-detail')
        @include($resource->detailView(), [
            'resource' => $resource,
            'item' => $item
        ])
    @endfragment
@endsection
