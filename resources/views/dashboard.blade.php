@extends("moonshine::layouts.app")

@section('title', config("moonshine.title"))

@section('content')
    @foreach(app(\Leeto\MoonShine\Dashboard\Dashboard::class)->getAssets('css') as $css)
        <link rel="stylesheet" href="{{ asset($css) }}">
    @endforeach

    @foreach(app(\Leeto\MoonShine\Dashboard\Dashboard::class)->getAssets('js') as $js)
        <script src="{{ asset($js) }}"></script>
    @endforeach

    @if($blocks)
    <div>
        @foreach($blocks as $block)
            <div class="flex items-center justify-between space-x-4 space-y-4">
                @foreach($block->items() as $item)
                    {!! $block->render($item) !!}
                @endforeach
            </div>
        @endforeach
    </div>
    @endif
@endsection

