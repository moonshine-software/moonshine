<div x-data="{
         range_from_{{ $element->id() }}: '{{ $element->value()[$element->fromField] ?? '' }}',
         range_to_{{ $element->id() }}: '{{ $element->value()[$element->toField] ?? '' }}'
     }"
     class="form-group form-group-inline"
>
    <x-moonshine::form.input
        :attributes="$element->attributes()->merge([
            'name' => $element->name($element->fromField)
        ])"
        x-bind:max="range_to_{{ $element->id() }}"
        x-model="range_from_{{ $element->id() }}"
    />

    <x-moonshine::form.input
        :attributes="$element->attributes()->merge([
            'name' => $element->name($element->toField)
        ])"
        x-bind:min="range_from_{{ $element->id() }}"
        x-model="range_to_{{ $element->id() }}"
    />
</div>
