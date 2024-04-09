@props([
    'current',
    'locales'
])
@if($locales->isNotEmpty())
    <x-moonshine::dropdown
        placement="bottom-end"
    >
        <x-slot:toggler>
            <a class="dropdown-btn btn">{{ $current }}</a>
        </x-slot:toggler>

        <ul class="dropdown-menu">
            @foreach($locales as $href => $locale)
                <li class="dropdown-menu-item">
                    <a
                        href="{{ $href }}"
                        class="dropdown-menu-link"
                    >
                        {{ $locale }}
                    </a>
                </li>
            @endforeach
        </ul>
    </x-moonshine::dropdown>
@endif
