<x-moonshine::column
    :colSpan="$item->columnSpanValue()"
    :adaptiveColSpan="$item->adaptiveColumnSpanValue()"
>
    @includeWhen($item->label(), 'moonshine::layouts.shared.title', [
        'title' => $item->label()
    ])

    {!! $item->text() !!}
</x-moonshine::column>
