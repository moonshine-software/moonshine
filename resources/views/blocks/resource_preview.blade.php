<x-moonshine::column
    :colSpan="$block->columnSpanValue()"
    :adaptiveColSpan="$block->adaptiveColumnSpanValue()"
>
    @if($element->label())
        <h5 class="text-md font-medium">
            <a href="{{ $element->resource()->route('index') }}">{{ $element->label() }}</a>
        </h5>
    @endif

    @include('moonshine::crud.shared.table', [
        'resource' => $element->resource(),
        'items' => $element->items(),
    ])
</x-moonshine::column>

