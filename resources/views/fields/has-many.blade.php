@include('moonshine::fields.shared.'.($field->isFullPage() ? 'full' : 'table').'-fields', [
    'field' => $field,
    'resource' => $resource,
    'item' => $item,
    'model' => $field->formViewValue($item)->first() ?? $item->{$field->relation()}()->getRelated(),
    'level' => $level ?? 0
])