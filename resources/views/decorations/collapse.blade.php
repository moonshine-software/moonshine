 <div
    x-data="{
        {{ $decoration->uniqid() }}_open: {{ $decoration->isShow() ? 'true' : 'false' }},
        {{ $decoration->uniqid() }}_toggle() {
            this.{{ $decoration->uniqid() }}_open = !this.{{ $decoration->uniqid() }}_open
        },
    }"
    class="border-b border-whiteblue dark:border-dark"
>
    <div
        x-on:click="{{ $decoration->uniqid() }}_toggle()"
        class="cursor-pointer flex items-center justify-between ml-5 mr-5 font-bold text-md text-black dark:text-white"
        :class="{{ $decoration->uniqid() }}_open ? 'mt-5' : 'my-5'"
    >
        {{ $decoration->label() }}
        <svg xmlns="http://www.w3.org/2000/svg"
             fill-opacity=".5"
             class="h-5 w-5"
             :class="{{ $decoration->uniqid() }}_open ? 'rotate-180' : ''"
             viewBox="0 0 20 20" fill="currentColor"
        >
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </div>
    <div
        x-show="{{ $decoration->uniqid() }}_open"
        x-transition.origin.top.left
        style="display: none;"
    >
        <x-moonshine::resource-renderable
            :components="$decoration->fields()"
            :item="$item"
            :resource="$resource"
            :container="true"
        />
    </div>
</div>
