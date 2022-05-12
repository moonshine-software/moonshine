@if($field->isSearchable())
    @include('moonshine::fields.multi-select', [
        'field' => $field,
        'item' => $item,
        'resource' => $resource
    ])
@else
    <select
            {!! $field->meta() ?? '' !!}
            id="{{ $field->id() }}"
            name="{{ $field->name() }}"
            {{ $field->isRequired() ? "required" : "" }}
            class="block appearance-none w-full border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
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