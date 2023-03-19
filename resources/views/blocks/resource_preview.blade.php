<div class="sm:col-span-{{ $item->adaptiveColumnSpanValue() }} xl:col-span-{{ $item->columnSpanValue() }}">
    @if($item->label())
        <h5 class="text-md font-medium">
            <a href="{{ $item->resource()->route('index') }}">{{ $item->label() }}</a>
        </h5>
    @endif

    @include('moonshine::crud.shared.table', [
        'resource' => $item->resource(),
        'items' => $item->items(),
    ])
</div>
