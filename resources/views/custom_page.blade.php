@extends('moonshine::layouts.app')

@section('title', $page->label())

@section('header-inner')
    @parent

    @include('moonshine::layouts.shared.breadcrumbs', [
        'items' => ['#' => $page->label()]
    ])
@endsection

@section('content')
    @include('moonshine::layouts.shared.title', ['title' => $page->label()])

    @includeIf($page->getView(), $page->getViewData())
@endsection

