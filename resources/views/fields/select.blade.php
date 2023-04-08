<x-moonshine::form.select
    :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'placeholder' => $element->label() ?? '',
        'name' => $element->name(),
    ])"
    :nullable="$element->isNullable()"
    :searchable="$element->isSearchable()"
    @class(['form-invalid' => $errors->has($element->name())])
>
    <x-slot:options>
        @if($element->isNullable())
            <option @selected(!$element->formViewValue($item)) value="">-</option>
        @endif

        @foreach($element->values() as $optionValue => $optionName)
            <option @selected($element->isSelected($item, $optionValue)) value="{{ $optionValue }}">
                {{ $optionName }}
            </option>
        @endforeach
    </x-slot:options>
</x-moonshine::form.select>
