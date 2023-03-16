<div class="sm:col-span-{{ $item->adaptiveColumnSpanValue() }} xl:col-span-{{ $item->columnSpanValue() }}">
    @includeWhen($item->label(), 'moonshine::layouts.shared.title', [
        'title' => $item->label()
    ])

    {!! $item->text() !!}
</div>
