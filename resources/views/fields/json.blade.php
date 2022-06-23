@include('moonshine::fields.shared.table-fields', [
    'field' => $field,
    'resource' => $resource,
    'item' => $item,
    'model' => $resource->getModel(),
    'level' => $level ?? 0
])