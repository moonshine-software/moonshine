@props([
    'searchable' => false,
    'nullable' => false,
    'values' => [],
    'options' => false,
    'asyncRoute' => null,
    'native' => false,
])

<select
        {{ $attributes->merge([
            'class' => 'form-select',
            'data-search-enabled' => $searchable,
            'data-remove-item-button' => $attributes->get('multiple', false) || $nullable
        ])->when(!$native, fn($a) => $a->merge([
            'x-data' => 'select(\''. $asyncRoute .'\')',
        ])) }}
>
    @if($options ?? false)
        {{ $options }}
    @else
        @if($nullable)
            <option value="">{{ $attributes->get('placeholder', '-') }}</option>
        @endif

        @foreach($values as $optionValue)
            @if(isset($optionValue['values']))
                <optgroup label="{{ $optionValue['label'] }}">
                    @foreach($optionValue['values'] as $oValue)
                        <option @selected($oValue['selected'] || $attributes->get('value', '') == $oValue['value'])
                                value="{{ $oValue['value'] }}"
                                data-custom-properties='@json($oValue['properties'])'
                        >
                            {{ $oValue['label'] }}
                        </option>
                    @endforeach
                </optgroup>
            @else
                <option @selected($optionValue['selected'] || $attributes->get('value', '') == $optionValue['value'])
                        value="{{ $optionValue['value'] }}"
                        data-custom-properties='@json($optionValue['properties'])'
                >
                    {{ $optionValue['label'] }}
                </option>
            @endif
        @endforeach
    @endif
</select>
