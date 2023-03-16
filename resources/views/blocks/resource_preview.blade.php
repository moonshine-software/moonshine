<div class="sm:col-span-{{ $item->adaptiveColumnSpanValue() }} xl:col-span-{{ $item->columnSpanValue() }}">
    @if($item->label())
        <div class="text-2xl mt-4">
            <a href="{{ $item->resource()->route('index') }}">{{ $item->label() }}</a>
        </div>
    @endif

    @include('moonshine::crud.shared.table', [
        'resource' => $item->resource(),
        'items' => $item->items(),
    ])
</div>
