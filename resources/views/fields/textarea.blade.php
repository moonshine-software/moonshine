<textarea
    {{ $element->attributes()->merge(['class' => 'text-black dark:text-white bg-white dark:bg-darkblue focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 px-4 block w-full appearance-none leading-normal']) }}
    id="{{ $element->id() }}"
    aria-label="{{ $element->label() ?? '' }}"
    placeholder="{{ $element->label() ?? '' }}"
    name="{{ $element->name() }}"

       {{ $element->isRequired() ? "required" : "" }}
    {{ $element->isDisabled() ? "disabled" : "" }}
    {{ $element->isReadonly() ? "readonly" : "" }}
>{!! $element->value() ?? '' !!}</textarea>
