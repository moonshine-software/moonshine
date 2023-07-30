<x-moonshine::form.select
        :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'placeholder' => $element->label() ?? '',
        'name' => $element->name(),
    ])"
        :nullable="$element->isNullable()"
        :searchable="$element->isSearchable()"
        @class(['form-invalid' => $errors->has($element->name())])
        :asyncRoute="(method_exists($element, 'isAsyncSearch') && $element->isAsyncSearch()) ?
            route('moonshine.search.relations', [
                    'column' => $element->field(),
                ]) : null"

>
    <x-slot:options>
        @if($element->isNullable())
            <option @selected(!$element->value()) value="">-</option>
        @endif
        @foreach($element->values() as $optionValue => $optionName)
            @if(is_array($optionName))
                <optgroup label="{{ $optionValue }}">
                    @foreach($optionName as $oValue => $oName)
                        <option @selected($element->isSelected($oValue))
                                value="{{ $oValue }}"
                        >
                            {{ $oName }}
                        </option>
                    @endforeach
                </optgroup>
            @else
                <option @selected($element->isSelected($optionValue))
                        value="{{ $optionValue }}"
                >
                    {{ $optionName }}
                </option>
            @endif
        @endforeach
    </x-slot:options>
</x-moonshine::form.select>
