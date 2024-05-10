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
            'data-remove-item-button' => $attributes->get('multiple', false) || $nullable,
            'data-loading-text' => $attributes->get('data-loading-text', __('moonshine::ui.loading')),
            'data-no-results-text' => $attributes->get('data-no-results-text', __('moonshine::ui.choices.no_results')),
            'data-no-choices-text' => $attributes->get('data-no-choices-text', __('moonshine::ui.choices.no_choices')),
            'data-item-select-text' => $attributes->get('data-item-select-text', __('moonshine::ui.choices.item_select')),
            'data-unique-item-text' => $attributes->get('data-unique-item-text', __('moonshine::ui.choices.unique_item')),
            'data-custom-add-item-text' => $attributes->get('data-custom-add-item-text', __('moonshine::ui.choices.custom_add_item')),
            'data-add-item-text' => $attributes->get('data-add-item-text', __('moonshine::ui.choices.add_item')),
            'data-max-item-text' => $attributes->get('data-max-item-text', trans_choice(
                'moonshine::ui.choices.max_item',
                $attributes->get('data-max-item-count', 0)
            )),
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
