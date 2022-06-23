@include('moonshine::fields.shared.table-fields', [
    'field' => $field,
    'resource' => $resource,
    'item' => $item,
    'model' => $field->formViewValue($item) ?? $resource->getModel(),
    'level' => $level ?? 0
])