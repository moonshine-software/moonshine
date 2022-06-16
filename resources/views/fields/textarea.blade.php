<textarea {!! $field->meta() ?? '' !!}
       id="{{ $field->id() }}"
       aria-label="{{ $field->label() ?? '' }}"
       placeholder="{{ $field->label() ?? '' }}"
       name="{{ $field->name() }}"
       class="{{ $field->getAttribute('class') ?? "text-black dark:text-white bg-white dark:bg-darkblue focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 px-4 block w-full appearance-none leading-normal" }}"

       {{ $field->isRequired() ? "required" : "" }}
       {{ $field->isDisabled() ? "disabled" : "" }}
       {{ $field->isReadonly() ? "readonly" : "" }}

       @if($field->getAutocomplete()) autocomplete="{{ $field->getAutocomplete() }}" @endif
>{!! $field->formViewValue($item) ?? '' !!}</textarea>