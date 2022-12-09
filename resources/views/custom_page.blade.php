@extends("moonshine::layouts.app")

@section('title', $page->label())

@section('header-inner')
    @parent
    @include("moonshine::shared.title", ["title" => $page->label()])
@endsection

@section('content')
    @includeIf($page->getView())
@endsection

