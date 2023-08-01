@extends('moonshine::layouts.app')

@section('sidebar-inner')
    @parent
@endsection

@section('header-inner')
    @parent

    @include('moonshine::layouts.shared.breadcrumbs', [
        'items' => $breadcrumbs
    ])

    @if(method_exists($resource, 'search') && $resource->search())
       <x-moonshine::search
           :action="$resource->currentRoute()"
       />
    @endif
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