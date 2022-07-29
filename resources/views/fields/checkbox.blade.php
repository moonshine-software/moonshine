<div>
    @if($field->isGroup())
        @include('moonshine::fields.multi-checkbox', [
            'field' => $field,
            'item' => $item,
            'resource' => $resource
        ])
    @else
        @include('moonshine::fields.shared.checkbox', [
            'attributes' => $field->attributes(),
            'id' => $field->id(),
            'name' => $field->name(),
            'value' => $field->formViewValue($item),
            'label' => $field->label()
        ])
    @endif
</div>
