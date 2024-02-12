@props([
    'persist' => false,
    'open' => false,
    'title'
])
<div
    {{ $attributes->class(['accordion']) }}
    x-data="{
        @if($persist)
            open: $persist({{ $open ? 'true' : 'false' }}).as($id('collapse')),
        @else
            open: {{ $open ? 'true' : 'false' }},
        @endif
        toggle() {
            this.open = !this.open
        }
    }"
>
    <div
        class="accordion-item"
        :class="open ? 'mt-5' : 'my-5'"
    >

        <h2 class="accordion-header">
            <button type="button" @click.prevent="toggle()" :class="{ '_is-active': open }"
                    class="accordion-btn btn"
                    type="button"
            >
                {!! $title !!}

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
        </h2>
        <div x-cloak x-show="open" class="accordion-body">
            <div class="accordion-content">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

