<div {{ $element->attributes() }}>
    @includeWhen($element->label(), 'moonshine::layouts.shared.title', [
        'title' => $element->label()
    ])

    {!! $element->text() !!}
</div>

