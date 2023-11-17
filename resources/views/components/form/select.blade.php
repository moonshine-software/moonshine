@props([
    'searchable' => false,
    'nullable' => false,
    'values' => [],
    'customProperties' => [],
    'options' => false,
    'asyncRoute' => null
])
<select
        {{ $attributes->merge([
            'class' => 'form-select',
            'x-data' => 'select(\''. $asyncRoute .'\')',
            'data-search-enabled' => $searchable,
            'data-remove-item-button' => $attributes->get('multiple', false) || $nullable
        ]) }}
>
    @if($options ?? false)
        {{ $options }}
    @else
        @if($nullable)
            <option value="">{{ $attributes->get('placeholder', '-') }}</option>
        @endif
        @foreach($values as $value => $label)
            @if(is_array($label))
                <optgroup label="{{ $value }}">
                    @foreach($label as $oValue => $oName)
                        <option @selected(is_selected_option($attributes->get('value', ''), $oValue))
                                value="{{ $oValue }}"
                                data-custom-properties='@json($customProperties[$oValue] ?? [])'
                        >
                            {{ $oName }}
                        </option>
                    @endforeach
                </optgroup>
            @else
                <option @selected(is_selected_option($attributes->get('value', ''), $value))
                        value="{{ $value }}"
                        data-custom-properties='@json($customProperties[$value] ?? [])'
                >
                    {{ $label }}
                </option>
            @endif
        @endforeach
    @endif
</select>
