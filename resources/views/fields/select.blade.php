<x-moonshine::form.select
        :attributes="$element->attributes()->merge([
        'id' => $element->id(),
        'placeholder' => $element->label() ?? '',
        'name' => $element->name(),
    ])"
        :nullable="$element->isNullable()"
        :searchable="$element->isSearchable()"
        @class(['form-invalid' => $errors->has($element->name())])
        :asyncRoute="(method_exists($element, 'isAsyncSearch') && $element->isAsyncSearch()) ?
            route('moonshine.search.relations', [
                    'resource' => $element->parent() && $element->parent()->resource()
                        ? $element->parent()->resource()->uriKey()
                        : $resource->uriKey(),
                    'column' => $element->field(),
                ]) : null"

>
    <x-slot:options>
        @if($element->isNullable())
            <option @selected(!$element->formViewValue($item)) value="">-</option>
        @endif
        @foreach($element->values() as $optionValue => $optionName)
            @if(is_array($optionName))
                <optgroup label="{{ $optionValue }}">
                    @foreach($optionName as $oValue => $oName)
                        <option @selected($element->isSelected($item, $oValue))
                                value="{{ $oValue }}"
                        >
                            {{ $oName }}
                        </option>
                    @endforeach
                </optgroup>
            @else
                <option @selected($element->isSelected($item, $optionValue))
                        value="{{ $optionValue }}"
                >
                    {{ $optionName }}
                </option>
            @endif
        @endforeach
    </x-slot:options>
</x-moonshine::form.select>
