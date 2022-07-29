@if($field->isSearchable())
    @include('moonshine::fields.multi-select', [
        'field' => $field,
        'item' => $item,
        'resource' => $resource
    ])
@else
    <select
            {{ $field->attributes()->merge(['class' => 'text-black dark:text-white bg-white dark:bg-darkblue block appearance-none w-full border border-gray-200 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:border-gray-500']) }}
            id="{{ $field->id() }}"
            name="{{ $field->name() }}"
            {{ $field->isRequired() ? "required" : "" }}
    >
            @if($field->isNullable())
                <option @selected(!$field->formViewValue($item)) value="">-</option>
            @endif

            @foreach($field->values() as $optionValue => $optionName)
                <option @selected($field->isSelected($item, $optionValue)) value="{{ $optionValue }}">
                    {{ $optionName }}
                </option>
            @endforeach
    </select>
@endif
