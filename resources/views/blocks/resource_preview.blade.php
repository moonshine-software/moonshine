<div class="w-full">
    @if($item->label())
        <div class="text-2xl mt-4">
            <a href="{{ $item->resource()->route('index') }}">{{ $item->label() }}</a>
        </div>
    @endif

    @include('moonshine::base.index.table', [
        'resource' => $item->resource(),
        'items' => $item->items(),
    ])
</div>
