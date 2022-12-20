<input {{ $field->attributes()->merge(['class' => 'text-black dark:text-white bg-white dark:bg-darkblue focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 px-4 block w-full appearance-none leading-normal']) }}
   id="{{ $field->id() }}"
   placeholder="{{ $field->label() ?? '' }}"
   name="{{ $field->name() }}"
   type="{{ $field->type() }}"

   {{ $field->isRequired() ? "required" : "" }}
   {{ $field->isDisabled() ? "disabled" : "" }}
   {{ $field->isReadonly() ? "readonly" : "" }}

   @if(!$field->isFile())
        @if(isset($valueKey))
            value="{!! is_array($field->formViewValue($item)) ? ($field->formViewValue($item)[$valueKey] ?? '') : ''  !!}"
        @else
            value="{!! (string) $field->formViewValue($item) ?? '' !!}"
        @endif
   @else
        accept="{{$field->acceptExtension()}}"
   @endif
/>
