@include('moonshine::fields.table-fields', [
    'element' => $element,
    'resource' => $resource,
    'item' => $item,
    'model' => $resource->getModel(),
    'level' => $level ?? 0
])
