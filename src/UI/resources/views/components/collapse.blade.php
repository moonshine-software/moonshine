@props([
    'components' => [],
    'persist' => false,
    'open' => false,
    'button' => null,
    'icon' => null,
    'title',
])
<div
    {{ $attributes->class(['accordion']) }}
    x-data="
        @if($persist)
            collapse($persist({{ $open ? 'true' : 'false' }}).as($id('collapse')))
        @else
            collapse({{ $open ? 'true' : 'false' }})
        @endif
    "
>
    <div class="accordion-item" :class="{ '_is-opened': open }">
        <button 
            type="button"
            class="accordion-btn"
            :class="{ '_is-active': open }"
            @click.prevent="toggle()"
        >
            <div class="flex gap-2 items-center">
                {{ $icon ?? '' }}
                {!! $title !!}
            </div>

            @if($button ?? false)
                {!! $button !!}
            @else
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            @endif
        </button>
        <div 
            x-cloak
            :class="open ? 'block' : 'hidden'"
            class="accordion-body"
        >
            <div class="accordion-content space-elements">
                <x-moonshine::components
                    :components="$components"
                />

                {{ $slot ?? '' }}
            </div>
        </div>
    </div>
</div>

