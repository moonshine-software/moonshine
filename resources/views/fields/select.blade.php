<x-moonshine::form.select
        :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'name' => $element->name(),
    ])"
        :nullable="$element->isNullable()"
        :searchable="$element->isSearchable()"
        :asyncRoute="$element->asyncUrl()"
        @class(['form-invalid' => formErrors($errors ?? false, $element->getFormName())->has($element->name())])
>
    <x-slot:options>
        @if($element->isNullable())
            <option value="">{{ $element->attributes()->get('placeholder', '-') }}</option>
        @endif
        @foreach($element->values() as $optionValue => $optionName)
            @if(is_array($optionName))
                <optgroup label="{{ $optionValue }}">
                    @foreach($optionName as $oValue => $oName)
                        <option @selected($element->isSelected($oValue))
                                value="{{ $oValue }}"
                                data-custom-properties='@json($element->getOptionProperties($oValue))'
                        >
                            {{ $oName }}
                        </option>
                    @endforeach
                </optgroup>
            @else
                <option @selected($element->isSelected($optionValue))
                        value="{{ $optionValue }}"
                        data-custom-properties='@json($element->getOptionProperties($optionValue))'
                >
                    {{ $optionName }}
                </option>
            @endif
        @endforeach
    </x-slot:options>
</x-moonshine::form.select>
