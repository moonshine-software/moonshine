<input {!! $field->meta() ?? '' !!}
   @if($field->getMask()) x-mask="{{ $field->getMask() }}" @endif
   id="{{ $field->id() }}"
   aria-label="{{ $field->label() ?? '' }}"
   placeholder="{{ $field->label() ?? '' }}"
   name="{{ $field->name() }}"
   type="{{ $field->type() }}"
   min="{{ $field->getAttribute('min') ?? 0 }}"
   max="{{ $field->getAttribute('max') ?? 100000 }}"
    max="{{ $field->getAttribute('step') ?? 1 }}"
   class="{{ $field->getAttribute('class') ?? "text-black bg-white focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 px-4 block w-full appearance-none leading-normal" }}"

   {{ $field->isRequired() ? "required" : "" }}
   {{ $field->isMultiple() ? "multiple" : "" }}
   {{ $field->isDisabled() ? "disabled" : "" }}
   {{ $field->isReadonly() ? "readonly" : "" }}

   @if($field->getAutocomplete()) autocomplete="{{ $field->getAutocomplete() }}" @endif
   @if($field->type() !== 'file')
        @if(isset($valueKey))
            value="{!! is_array($field->formViewValue($item)) ? ($field->formViewValue($item)[$valueKey] ?? '') : ''  !!}"
        @else
            value="{!! (string) $field->formViewValue($item) ?? '' !!}"
        @endif
   @endif
/>