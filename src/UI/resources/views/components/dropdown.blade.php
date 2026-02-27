@props([
    'items' => null,
    'placement' => 'bottom-start',
    'toggler',
    'title',
    'titleRaw' => false,
    'itemsRaw' => false,
    'searchable' => false,
    'searchPlaceholder' => '',
    'footer' => null,
    'footerRaw' => false,
    'strategy' => 'fixed',
])
<div x-data="dropdown"
     @click.outside="closeDropdown"
     data-dropdown-strategy="{{ $strategy }}"
     data-dropdown-placement="{{ $placement }}"
     data-searchable="{{$searchable}}"
     class="dropdown"
>
    <button type="button" @click.prevent="toggleDropdown" {{ $toggler->attributes->merge(['class' => 'dropdown-btn']) }}>
        {{ $toggler }}
    </button>

    <div {{ $attributes->merge(['class' => 'dropdown-body']) }}>
        @if($title ?? false)
            <div class="dropdown-heading">
                @if($titleRaw)
                    {!! $title !!}
                @else
                    {{ $title }}
                @endif
            </div>
        @endif

        <div class="dropdown-content">
            @if($slot->isNotEmpty())
                {{ $slot }}
            @endif

            @if(!empty($items))
                @if($searchable)
                    <x-moonshine::form.input
                        x-model.debounce.500ms="dropdownSearch"
                        class="dropdown-input-search"
                        placeholder="{{ $searchPlaceholder }}"
                    ></x-moonshine::form.input>
                @endif

                <ul class="dropdown-menu">
                    @foreach($items as $key =>$item)
                        <li
                            class="dropdown-menu-item"
                            @if($searchable) x-ref="dropdown_{{$key}}" @endif
                        >
                            @if($itemsRaw)
                                {!! $item !!}
                            @else
                                {{ $item }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if($footer ?? false)
            <div class="dropdown-footer">
                @if($footerRaw)
                    {!! $footer ?? '' !!}
                @else
                    {{ $footer ?? '' }}
                @endif
            </div>
        @endif
    </div>
</div>
