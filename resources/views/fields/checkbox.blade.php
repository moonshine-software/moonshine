<div>
    @if($field->isMultiple())
        @include('moonshine::fields.multi-checkbox', [
            'field' => $field,
            'item' => $item,
            'resource' => $resource
        ])
    @else
        @include('moonshine::fields.shared.checkbox', [
            'meta' => $field->meta(),
            'id' => $field->id(),
            'name' => $field->name(),
            'value' => $field->formViewValue($item),
            'label' => $field->label()
        ])
    @endif
</div>
