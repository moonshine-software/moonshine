@extends($layout)

@section('header-inner')
    @parent

    @includeWhen(!empty($breadcrumbs), 'moonshine::layouts.shared.breadcrumbs', [
        'items' => $breadcrumbs
    ])

    @if(!is_null($resource) && method_exists($resource, 'search') && $resource->search())
       <x-moonshine::search
           :action="to_page(resource: $resource)"
       />
    @endif
@endsection

@section('content')
    @includeWhen($title, 'moonshine::layouts.shared.title', [
        'title' => $title,
        'subTitle' => $subtitle
    ])

    <x-moonshine::components :components="$components" />
@endsection
