<div>
    @include('moonshine::fields.' . ($field->isGroup() ? 'multi-checkbox' : 'input'), [
        'field' => $field,
        'item' => $item,
        'resource' => $resource
    ])
</div>
