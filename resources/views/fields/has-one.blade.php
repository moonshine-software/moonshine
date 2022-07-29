@include('moonshine::fields.shared.'.($field->isFullPage() ? 'full' : 'table').'-fields', [
    'field' => $field,
    'resource' => $resource,
    'item' => $item,
    'model' => $field->formViewValue($item) ?? $field->getRelated($item),
    'level' => $level ?? 0
])
