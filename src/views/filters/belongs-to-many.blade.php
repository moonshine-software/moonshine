@include('moonshine::fields.belongs-to-many', [
    'field' => $field,
    'resource' => $resource,
    'item' => $item,
    'model' => $resource->getModel()
])