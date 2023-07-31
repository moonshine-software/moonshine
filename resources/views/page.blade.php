@extends('moonshine::layouts.app')

@section('sidebar-inner')
    @parent
@endsection

@section('header-inner')
    @parent

    @include('moonshine::layouts.shared.breadcrumbs', [
        'items' => $breadcrumbs
    ])

    @includeWhen(
        $resource->search(),
        'moonshine::crud.shared.search'
    )
@endsection

@section('content')
    @include('moonshine::layouts.shared.title', [
        'title' => $title,
        'subTitle' => $subtitle
    ])

    @foreach($components as $component)
        {{ $component->render() }}
    @endforeach
@endsection