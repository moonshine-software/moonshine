@props([
    'current',
    'locales'
])
@if($locales->isNotEmpty())
<!-- Languages -->
<div class="languages">
    <x-moonshine::dropdown placement="bottom-end">
        <x-slot:toggler>
            <a 
                class="languages-btn dropdown-btn"
                :class="open && '_is-opened'"
            >
                <x-moonshine::icon 
                    icon="language"
                    class="languages-btn-icon"
                />
                <x-moonshine::icon 
                    icon="chevron-down"
                    class="languages-btn-arrow"
                />
            </a>
        </x-slot:toggler>

        <ul class="languages-menu dropdown-menu">
            @foreach($locales as $href => $locale)
                <li class="languages-item dropdown-menu-item">
                    <a
                        href="{{ $href }}"
                        class="languages-link dropdown-menu-link"
                    >
                        {{ $locale }}
                    </a>
                </li>
            @endforeach
        </ul>
    </x-moonshine::dropdown>
</div>
<!-- END: Languages -->
@endif
