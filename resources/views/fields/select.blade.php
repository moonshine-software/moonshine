@if($element->isSearchable())
    @include('moonshine::fields.multi-select', [
        'element' => $element,
    ])
@else
    <select
        {{ $element->attributes()->merge(['class' => 'text-black dark:text-white bg-white dark:bg-darkblue block appearance-none w-full border border-gray-200 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:border-gray-500']) }}
        id="{{ $element->id() }}"
        name="{{ $element->name() }}"
        {{ $element->isRequired() ? "required" : "" }}
    >
        @if($element->isNullable())
            <option @selected(!$element->value()) value="">-</option>
        @endif

        @foreach($element->values() as $optionValue => $optionName)
            <option @selected($element->isSelected($optionValue)) value="{{ $optionValue }}">
                {{ $optionName }}
            </option>
        @endforeach
    </select>
@endif
