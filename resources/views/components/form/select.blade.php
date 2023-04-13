@props([
    'searchable' => false,
    'nullable' => false,
    'values' => [],
    'options' => false,
    'fetchRoute'=>''
])
<select
    {{ $attributes->merge([
        'class' => 'form-select',
        'x-data' => $fetchRoute ? 'select(\''.$fetchRoute.'\')' : 'select',
        'data-search-enabled' => $searchable,
        'data-remove-item-button' => $nullable
    ]) }}
>
    @if($options ?? false)
        {{ $options }}
    @else
        @foreach($values as $value => $label)
            <option @selected($value == $attributes->get('value', ''))
                    value="{{ $value }}"
            >
                {{ $label }}
            </option>
        @endforeach
    @endif
</select>
