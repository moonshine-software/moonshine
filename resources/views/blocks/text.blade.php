<x-moonshine::column
    :colSpan="$item->adaptiveColumnSpanValue()"
    :adaptiveColSpan="$item->columnSpanValue()"
>
    @includeWhen($item->label(), 'moonshine::layouts.shared.title', [
        'title' => $item->label()
    ])

    {!! $item->text() !!}
</x-moonshine::column>
