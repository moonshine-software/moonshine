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
@endsection

@section('header-inner')
    @parent
    @include("moonshine::shared.title", ["title" => $resource->title()])
@endsection

@section('content')
    <div class="mt-8"></div>

    <div class="flex flex-col mt-8">
        <div x-data="editForm()">
            <div x-data="{activeTab: '{{ $resource->tabs()->first()?->id() }}'}">
                @if($resource->tabs()->isNotEmpty())
                    <div>
                        <nav class="flex flex-col sm:flex-row">
                            @foreach($resource->tabs() as $tab)
                                <button
                                    :class="{ 'border-b-2 font-medium border-purple': activeTab === '{{ $tab->id() }}' }"
                                    @click.prevent="activeTab = '{{ $tab->id() }}'"
                                    class="py-4 px-6 block focus:outline-none text-purple">
                                    {{ $tab->label() }}
                                </button>
                            @endforeach
                        </nav>
                    </div>
                @endif

                {!! $form !!}
            </div>

            <script>
                function editForm() {
                    return {
                        @if($resource->whenFieldNames())
                            @foreach($resource->whenFieldNames() as $name)
                            {{ $name }}: '{{ $item->{$name} }}',
                        @endforeach
                        @endif
                    };
                }
            </script>
        </div>
    </div>

    @include("moonshine::changelog", ["resource" => $resource, "item" => $item])
@endsection
