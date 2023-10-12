<x-moonshine::form.select
        :attributes="$element->attributes()->except('x-on:change')->merge([
        'id' => $element->id(),
        'name' => $element->name(),
    ])"
        :nullable="$element->isNullable()"
        :searchable="$element->isSearchable()"
        :asyncRoute="$element->asyncUrl()"
        :@change="(($updateOnPreview ?? false)
            ? 'updateColumn(
                `'.$element->getUpdateOnPreviewUrl().'`,
                `'.$element->column().'`
            )'
            : $element->attributes()->get('x-on:change')
        )"
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
