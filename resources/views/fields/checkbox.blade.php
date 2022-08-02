<div>
    @if($element->isGroup())
        @include('moonshine::fields.multi-checkbox', [
            'element' => $element
        ])
    @else
        @include('moonshine::fields.shared.checkbox', [
            'attributes' => $element->attributes(),
            'id' => $element->id(),
            'name' => $element->name(),
            'value' => $element->value(),
            'label' => $element->label()
        ])
    @endif
</div>
