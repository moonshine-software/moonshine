@props([
    'route' => '',
    'logOutRoute' => '',
    'avatar' => '',
    'nameOfUser' => '',
    'username' => '',
    'menu' => null,
    'translates' => [],
    'before',
    'after',
])
{{ $before ?? '' }}

@if(isset($slot) && $slot->isNotEmpty())
    {{ $slot }}
@else
    <div {{ $attributes->merge(['class' => 'profile']) }}>
        @if($route && $menu === null)
        <a href="{{ $route }}" class="profile-main">
        @endif
            @if($avatar && $menu === null)
                <div class="profile-photo">
                    <img
                        class="h-full w-full object-cover"
                        src="{{ $avatar }}"
                        alt="{{ $nameOfUser }}"
                    />
                </div>
            @endif

            @if($menu === null)
                <div class="profile-info">
                    <h5 class="name">{{ $nameOfUser }}</h5>
                    <div class="email">{{ $username }}</div>
                </div>
            @endif
        @if($route && $menu === null)
        </a>
        @endif

        @if($menu === null)
            @if($logOutRoute)
                <x-moonshine::form class="profile-actions" :action="$logOutRoute" :raw="true">
                    <x-moonshine::form.input type="hidden" name="_method" value="delete" />

                    <x-moonshine::form.button
                        :raw="true"
                        class="profile-exit btn-fit"
                        title="Logout"
                        type="submit"
                    >
                        <x-moonshine::icon icon="arrow-right-start-on-rectangle" />
                    </x-moonshine::form.button>
                </x-moonshine::form>
            @endif
        @else
            <x-moonshine::dropdown>
                <x-slot:title>
                    <div class="profile-main">
                        @if($avatar)
                            <div class="profile-photo">
                                <img
                                    class="h-full w-full object-cover"
                                    src="{{ $avatar }}"
                                    alt="{{ $nameOfUser }}"
                                />
                            </div>
                        @endif

                        <div class="profile-info">
                            <h5 class="name">{{ $nameOfUser }}</h5>
                            <div class="email">{{ $username }}</div>
                        </div>
                    </div>
                </x-slot:title>

                <x-slot:toggler>
                    <div class="profile-photo">
                        <img
                            class="h-full w-full object-cover"
                            src="{{ $avatar }}"
                            alt="{{ $nameOfUser }}"
                        />
                    </div>
                </x-slot:toggler>

                @if($logOutRoute)
                    <x-slot:footer>
                        <x-moonshine::form :action="$logOutRoute" :raw="true">
                            <x-moonshine::form.input type="hidden" name="_method" value="delete" />
                            <x-moonshine::form.button
                                :raw="true"
                                class="btn"
                                type="submit"
                            >
                                <x-moonshine::icon icon="arrow-right-start-on-rectangle" />
                                {{ $translates['logout'] ?? 'Log out' }}
                            </x-moonshine::form.button>
                        </x-moonshine::form>
                    </x-slot:footer>
                @endif

                @if(is_iterable($menu))
                    <ul class="dropdown-menu">
                        @foreach($menu as $link)
                            <li class="dropdown-menu-item">
                                {!! $link !!}
                            </li>
                        @endforeach
                    </ul>
                @else
                    {{ $menu }}
                @endif
            </x-moonshine::dropdown>
        @endif
    </div>
@endif

{{ $after ?? '' }}

