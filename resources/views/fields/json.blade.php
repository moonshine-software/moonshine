@include('moonshine::fields.table-fields', [
    'field' => $field,
    'resource' => $resource,
    'item' => $item,
    'model' => $resource->getModel(),
    'level' => $level ?? 0
])
