@extends("moonshine::layouts.app")

@section('sidebar-inner')
    @parent

    <div class="text-center mt-8">
        @include('moonshine::shared.btn', [
            'title' => trans('moonshine::ui.back'),
            'href' => $resource->route("index"),
            'filled' => true,
            'icon' => false,
        ])
    </div>
@endsection

@section('header-inner')
    @parent
    @include("moonshine::shared.title", ["title" => $resource->title()])
    @include("moonshine::shared.sub-title", ["subTitle" => $resource->subTitle()])
@endsection

@section('content')
    <div class="mt-8"></div>

    <div class="flex flex-col mt-8">
        @include("moonshine::base.show.show", ["item" => $item])
    </div>
@endsection
