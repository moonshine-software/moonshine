<x-moonshine::form.select
    :attributes="$field->attributes()->merge([
        'id' => $field->id(),
        'placeholder' => $field->label() ?? '',
        'name' => $field->name(),
        'type' => $field->type(),
    ])"
    :nullable="$field->isNullable()"
>
    <x-slot:options>
        @if($field->isNullable())
            <option @selected(!$field->formViewValue($item)) value="">-</option>
        @endif

        @foreach($field->values() as $optionValue => $optionName)
            <option @selected($field->isSelected($item, $optionValue)) value="{{ $optionValue }}">
                {{ $optionName }}
            </option>
        @endforeach
    </x-slot:options>
</x-moonshine::form.select>
