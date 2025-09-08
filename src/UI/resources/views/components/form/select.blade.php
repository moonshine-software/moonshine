@props([
    'searchable' => false,
    'nullable' => false,
    'values' => [],
    'options' => false,
    'asyncRoute' => null,
    'native' => false,
    'settings' => [],
    'plugins' => [],
])

<select
        {{ $attributes->merge([
            'class' => $native ? 'form-select' : null,
            'data-search-enabled' => $searchable,
            'data-remove-item-button' => $attributes->get('multiple', false) || $nullable
        ])->when(!$native, fn($a) => $a->merge([
            'x-data' => "select('$asyncRoute', ". json_encode($settings) .", ". json_encode($plugins) .")",
        ]))->when($nullable && !$native && $attributes->get('placeholder') === null, fn($a) => $a->merge(['placeholder' => '-'])) }}
>
    @if($options ?? false)
        {{ $options }}
    @else
        @if($nullable && !$attributes->has('multiple'))
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
                        data-custom-properties='@json(['customProperties' => $optionValue['properties']])'
                >
                    {{ $optionValue['label'] }}
                </option>
            @endif
        @endforeach
    @endif
</select>