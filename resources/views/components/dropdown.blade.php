@props([
    'items' => null,
    'placement' => 'bottom-start',
    'toggler',
    'title',
    'searchable' => false,
    'footer' => null,
])
<div x-data="dropdown"
     @click.outside="closeDropdown"
     data-dropdown-placement="{{ $placement }}"
     data-searchable="{{$searchable}}"
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
                @if($searchable)
                    <x-moonshine::form.input x-model.debounce.500ms="dropdownSearch"></x-moonshine::form.input>
                @endif
                <ul class="dropdown-menu">
                    @foreach($items as $key =>$item)
                        <li
                            class="dropdown-menu-item p-2"
                            @if($searchable) x-ref="dropdown_{{$key}}"@endif
                        >
                            {!! $item !!}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if($footer?->isNotEmpty())
            <div class="dropdown-footer">
                {{ $footer ?? '' }}
            </div>
        @endif
    </div>
</div>
