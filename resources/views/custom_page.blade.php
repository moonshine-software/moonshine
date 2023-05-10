@extends($page->getLayout())

@section('title', $page->label())

@section('header-inner')
    @parent

    @include('moonshine::layouts.shared.breadcrumbs', [
        'items' => $page->getBreadcrumbs()
    ])
@endsection

@section('content')
    @includeWhen($page->withTitle(), 'moonshine::layouts.shared.title', ['title' => $page->label()])

    @includeIf($page->getView(), $page->getViewData())
@endsection

