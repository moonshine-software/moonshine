<div>
    @include('moonshine::fields.shared.checkbox', [
        'meta' => $field->meta(),
        'id' => $field->id(),
        'name' => $field->name(),
        'value' => $field->formViewValue($item),
        'label' => $field->label()
    ])
</div>
