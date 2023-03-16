@include('moonshine::fields.select', [
    'field' => $field,
    'resource' => $resource,
    'item' => $resource->getModel()
])
