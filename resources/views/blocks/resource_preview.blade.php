<x-moonshine::column
    :colSpan="$item->adaptiveColumnSpanValue()"
    :adaptiveColSpan="$item->columnSpanValue()"
>
    @if($item->label())
        <h5 class="text-md font-medium">
            <a href="{{ $item->resource()->route('index') }}">{{ $item->label() }}</a>
        </h5>
    @endif

    @include('moonshine::crud.shared.table', [
        'resource' => $item->resource(),
        'items' => $item->items(),
    ])
</x-moonshine::column>

