@extends("moonshine::layouts.app")

@section('title', config("moonshine.title"))

@section("header-inner")
    @parent

    @include('moonshine::layouts.shared.breadcrumbs', [
        'items' => [route('moonshine.index') => 'Dashboard']
    ])
@endsection

@section('content')
    @if($blocks)
        <div class="flex flex-col gap-y-8 gap-x-6 sm:grid sm:grid-cols-12 lg:gap-y-10">
            @foreach($blocks as $block)
                <x-moonshine::column
                    :colSpan="$block->adaptiveColumnSpanValue()"
                    :adaptiveColSpan="$block->columnSpanValue()"
                >
                    @if($block->label())
                        <h2 class="mb-4 truncate text-md font-medium">
                            {{ $block->label() }}
                        </h2>
                    @endif

                    <div class="flex flex-col gap-y-8 gap-x-6 sm:grid sm:grid-cols-12 lg:gap-y-10">
                        @foreach($block->items() as $item)
                            {!! $block->render($item) !!}
                        @endforeach
                    </div>
                </x-moonshine::column>
            @endforeach
        </div>
    @endif
@endsection

