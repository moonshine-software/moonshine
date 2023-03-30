<div
    x-data="{
        {{ $element->id() }}_open: $persist({{ $element->isShow() ? 'true' : 'false' }}),
        {{ $element->id() }}_toggle() {
            this.{{ $element->id() }}_open = !this.{{ $element->id() }}_open
        },
    }"
    class="accordion"
>
    <div
        class="accordion-item"
        :class="{{ $element->id() }}_open ? 'mt-5' : 'my-5'"
    >

        <h2 class="accordion-header">
            <button @click.prevent="{{ $element->id() }}_toggle()" :class="{ '_is-active': {{ $element->id() }}_open }"
                    class="accordion-btn btn"
                    type="button"
            >
                {{ $element->label() }}

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
        </h2>
        <div x-show="{{ $element->id() }}_open" class="accordion-body">
            <div class="accordion-content">
                <x-moonshine::resource-renderable
                    :components="$element->getFields()"
                    :item="$item"
                    :resource="$resource"
                    :container="true"
                />
            </div>
        </div>
    </div>
</div>
