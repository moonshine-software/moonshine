@if($element->isSearchable())
    @include('moonshine::fields.multi-select', [
        'element' => $element
    ])
@else
    @include('moonshine::fields.select', [
        'element' => $element
    ])
@endif
