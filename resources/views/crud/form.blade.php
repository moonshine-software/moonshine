@extends("moonshine::layouts.app")

@section('sidebar-inner')
    @parent
@endsection

@section('header-inner')
    @parent

    @include('moonshine::layouts.shared.breadcrumbs', [
        'items' => [
            $resource->route('index') => $resource->title(),
            '#' => $item->getKey() ?? trans('moonshine::ui.create')
        ]
    ])
@endsection

@section('content')
    @include('moonshine::crud.shared.form', ['item' => $item])

    @include('moonshine::crud.shared.changelog', ['resource' => $resource, 'item' => $item])
@endsection
