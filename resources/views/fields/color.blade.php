<div x-data="{ color: '{!! (string) $field->formViewValue($item) ?? "#000000" !!}' }">
    <div class="flex flex-row justify-start items-center">
        <div class="relative -mr-10 ml-2 w-8 h-8 rounded-full overflow-hidden">
            <input
                type="color"
                x-model:value="color"
                class="absolute -top-2 -left-2 w-16 h-16 rounded-full"
                {{ $field->isRequired() ? "required" : "" }}
                {{ $field->isDisabled() ? "disabled" : "" }}
                {{ $field->isReadonly() ? "readonly" : "" }}
            >
        </div>
        <input
            id="{{ $field->id() }}"
            type="text"
            name="{{ $field->name() }}"
            x-model:value="color"
            class="text-black dark:text-white bg-white dark:bg-darkblue focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 pl-12 pr-4 block w-full appearance-none leading-normal"
            placeholder="#000000"
            {{ $field->isRequired() ? "required" : "" }}
            {{ $field->isDisabled() ? "disabled" : "" }}
            {{ $field->isReadonly() ? "readonly" : "" }}
        >
    </div>
</div>
