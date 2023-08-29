@props([
    'searchable' => false,
    'nullable' => false,
    'values' => [],
    'options' => false,
    'asyncRoute' => null
])
<select
        {{ $attributes->merge([
            'class' => 'form-select',
            'x-data' => 'select(\''. $asyncRoute .'\')',
            'data-search-enabled' => $searchable,
            'data-remove-item-button' => $nullable
        ]) }}
>
    @if($options ?? false)
        {{ $options }}
    @else
        @foreach($values as $value => $label)
            @if(is_array($label))
                <optgroup label="{{ $value }}">
                    @foreach($label as $oValue => $oName)
                        <option @selected(is_selected_option($attributes->get('value', ''), $oValue))
                                value="{{ $oValue }}"
                        >
                            {{ $oName }}
                        </option>
                    @endforeach
                </optgroup>
            @else
                <option @selected(is_selected_option($attributes->get('value', ''), $value))
                        value="{{ $value }}"
                >
                    {{ $label }}
                </option>
            @endif
        @endforeach
    @endif
</select>
