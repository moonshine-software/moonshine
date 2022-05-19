@if($field->isSearchable())
    @include('moonshine::fields.multi-select', [
        'field' => $field,
        'resource' => $resource,
        'item' => $resource->getModel()
    ])
@else
    @include('moonshine::fields.select', [
        'field' => $field,
        'resource' => $resource,
        'item' => $resource->getModel()
    ])
@endif