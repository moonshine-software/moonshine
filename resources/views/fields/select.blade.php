@props([
    'value' => '',
    'values' => [],
    'isNullable' => false,
    'isSearchable' => false,
    'isSelected' => static fn() => false,
    'optionProperties' => static fn() => [],
    'asyncUrl' => '',
])
<x-moonshine::form.select
        :attributes="$attributes"
        :nullable="$isNullable"
        :searchable="$isSearchable"
        :asyncRoute="$asyncUrl"
>
    <x-slot:options>
        @if($isNullable)
            <option value="">{{ $attributes->get('placeholder', '-') }}</option>
        @endif
        @foreach($values as $optionValue => $optionName)
            @if(is_array($optionName))
                <optgroup label="{{ $optionValue }}">
                    @foreach($optionName as $oValue => $oName)
                        <option @selected($isSelected($oValue))
                                value="{{ $oValue }}"
                                data-custom-properties='@json($optionProperties($oValue))'
                        >
                            {{ $oName }}
                        </option>
                    @endforeach
                </optgroup>
            @else
                <option @selected($isSelected($optionValue))
                        value="{{ $optionValue }}"
                        data-custom-properties='@json($optionProperties($optionValue))'
                >
                    {{ $optionName }}
                </option>
            @endif
        @endforeach
    </x-slot:options>
</x-moonshine::form.select>
