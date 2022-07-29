<textarea {{ $field->attributes()->merge(['class' => 'text-black dark:text-white bg-white dark:bg-darkblue focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 px-4 block w-full appearance-none leading-normal']) }}
       id="{{ $field->id() }}"
       aria-label="{{ $field->label() ?? '' }}"
       placeholder="{{ $field->label() ?? '' }}"
       name="{{ $field->name() }}"

       {{ $field->isRequired() ? "required" : "" }}
       {{ $field->isDisabled() ? "disabled" : "" }}
       {{ $field->isReadonly() ? "readonly" : "" }}
>{!! $field->formViewValue($item) ?? '' !!}</textarea>
