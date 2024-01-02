@props([
    'items' => null,
    'placement' => 'bottom-start',
    'toggler',
    'title',
    'footer'
])
<div x-data="dropdown"
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

            @if(!empty($items))
                <ul class="dropdown-menu">
                    @foreach($items as $item)
                        <li class="dropdown-menu-item p-2">
                            {!! $item !!}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if($footer ?? false)
            <div class="dropdown-footer">
                {{ $footer ?? '' }}
            </div>
        @endif
    </div>
</div>
