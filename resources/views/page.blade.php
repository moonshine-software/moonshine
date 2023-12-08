@extends($layout)

@section('header-inner')
    @parent

    @includeWhen(!empty($breadcrumbs), 'moonshine::layouts.shared.breadcrumbs', [
        'items' => $breadcrumbs
    ])
@endsection

@section('content')
    @includeWhen($title, 'moonshine::layouts.shared.title', [
        'title' => $title,
        'subTitle' => $subtitle
    ])

    @includeWhen($contentView ?? false, $contentView)

    <x-moonshine::components :components="$components" />
@endsection
