<div
    x-data="{
        {{ $decoration->id() }}_open: $persist({{ $decoration->isShow() ? 'true' : 'false' }}),
        {{ $decoration->id() }}_toggle() {
            this.{{ $decoration->id() }}_open = !this.{{ $decoration->id() }}_open
        },
    }"
    class="accordion"
>
    <div
        x-on:click="{{ $decoration->id() }}_toggle()"
        class="accordion-item"
        :class="{{ $decoration->id() }}_open ? 'mt-5' : 'my-5'"
    >

        <h2 class="accordion-header">
            <button @click.prevent="{{ $decoration->id() }}_toggle()" :class="{ '_is-active': {{ $decoration->id() }}_open }"
                    class="accordion-btn btn"
                    type="button"
            >
                {{ $decoration->label() }}

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
        </h2>
        <div x-show="{{ $decoration->id() }}_open" class="accordion-body" style="display: none">
            <div class="accordion-content">
                <x-moonshine::resource-renderable
                    :components="$decoration->fields()"
                    :item="$item"
                    :resource="$resource"
                    :container="true"
                />
            </div>
        </div>
    </div>
</div>
