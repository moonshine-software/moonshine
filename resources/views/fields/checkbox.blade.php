<div>
    @include('moonshine::fields.' . ($element->isGroup() ? 'multi-checkbox' : 'input'), [
        'element' => $element,
        'item' => $item,
        'resource' => $resource
    ])
</div>
