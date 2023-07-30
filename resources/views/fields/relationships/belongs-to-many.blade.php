@include('moonshine::fields.' . (method_exists($element, 'isSelect') && $element->isSelect() ? 'select' : 'multi-checkbox'), [
    'element' => $element,
    'resource' => $resource,
    'item' => $item,
    'model' => $element->value() ?? $resource->getModel()
])
