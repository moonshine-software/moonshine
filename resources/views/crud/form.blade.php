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
        @if(!$resource->isRelatable())
            @include('moonshine::crud.shared.metrics', compact('metrics'))
        @endif
    @endif
    @fragment('crud-form')
        @include($resource->formView(), ['item' => $item])
    @endfragment
@endsection
