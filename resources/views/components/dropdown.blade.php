@props([
    'placement' => 'bottom-start',
    'toggler',
    'title',
    'footer'
])
<div x-data="dropdown" x-ref="dropdownEl"
     @click.outside="closeDropdown"
     data-dropdown-placement="{{ $placement }}"
     class="dropdown"
>
    <button type="button" @click.prevent="toggleDropdown" {{ $toggler->attributes->merge(['class' => 'dropdown-btn']) }}>
        {{ $toggler }}
    </button>

    <div {{ $attributes->merge(['class' => 'dropdown-body']) }}>
        @if($title ?? false)
            <h5 class="dropdown-heading">{{ $title }}</h5>
        @endif

        <div class="dropdown-content">
            {{ $slot }}
        </div>

        @if($footer ?? false)
            <div class="dropdown-footer">
                {{ $footer ?? '' }}
            </div>
        @endif
    </div>
</div>
