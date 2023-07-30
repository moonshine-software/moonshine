@extends("moonshine::layouts.app")

@section('title', config("moonshine.title"))

@section("header-inner")
    @parent

    @include('moonshine::layouts.shared.breadcrumbs', [
        'items' => ['#' => __('moonshine::ui.dashboard')]
    ])
@endsection

@section('content')
    @foreach($components as $component)
        {{ $component->render() }}
    @endforeach
@endsection

