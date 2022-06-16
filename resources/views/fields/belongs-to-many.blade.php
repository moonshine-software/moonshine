@include('moonshine::fields.' . (method_exists($field, 'isSelect') && $field->isSelect() ? 'select' : 'multi-checkbox'), [
    'field' => $field,
    'resource' => $resource,
    'item' => $item,
    'model' => $field->formViewValue($item) ?? $resource->getModel()
])