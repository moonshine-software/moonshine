<x-moonshine::column
    :colSpan="$block->columnSpanValue()"
    :adaptiveColSpan="$block->adaptiveColumnSpanValue()"
>
    @includeWhen($element->label(), 'moonshine::layouts.shared.title', [
        'title' => $element->label()
    ])

    {!! $element->text() !!}
</x-moonshine::column>
