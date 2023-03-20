@if(config('moonshine.locales'))
    <x-moonshine::dropdown
        placement="bottom-end"
    >
        <x-slot:toggler>
            <a class="dropdown-btn btn">{{ app()->getLocale() }}</a>
        </x-slot:toggler>

        <ul class="dropdown-menu">
            @foreach(config('moonshine.locales') as $lang)
                <li class="dropdown-menu-item">
                    <a
                        href="?change-moonshine-locale={{ $lang }}"
                        class="dropdown-menu-link"
                    >
                        {{ $lang }}
                    </a>
                </li>
            @endforeach
        </ul>
    </x-moonshine::dropdown>
@endif
