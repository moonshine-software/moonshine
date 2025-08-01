@props([
    'route' => '',
    'logOutRoute' => '',
    'avatar' => '',
    'nameOfUser' => '',
    'username' => '',
    'withBorder' => false,
    'menu' => null,
    'translates' => [],
    'before',
    'after',
])
@if($withBorder) <div {{ $attributes->merge(['class' => 'mt-2 border-t border-dark-200']) }}> @endif
    {{ $before ?? '' }}

    @if(isset($slot) && $slot->isNotEmpty())
        {{ $slot }}
    @else
        <div class="profile">
            @if($route)
            <a href="{{ $route }}"
               class="profile-main"
            >
            @endif
                @if($avatar)
                    <div class="profile-photo">
                        <img class="h-full w-full object-cover"
                             src="{{ $avatar }}"
                             alt="{{ $nameOfUser }}"
                        />
                    </div>
                @endif

                <div class="profile-info">
                    <h5 class="name">{{ $nameOfUser }}</h5>
                    <div class="email">{{ $username }}</div>
                </div>
            @if($route)
            </a>
            @endif

            @if($menu === null)
                @if($logOutRoute)
                    <x-moonshine::form :action="$logOutRoute" :raw="true">
                        <x-moonshine::form.input type="hidden" name="_method" value="delete" />

                        <x-moonshine::form.button
                            :raw="true"
                            class="profile-exit"
                            title="Logout"
                            type="submit"
                        >
                            <x-moonshine::icon
                                icon="power"
                                color="gray"
                                size="6"
                            />
                        </x-moonshine::form.button>
                    </x-moonshine::form>
                @endif
            @else
                <x-moonshine::dropdown>
                    <x-slot:title>
                        <div class="profile-main">
                            @if($avatar)
                                <div class="profile-photo">
                                    <img class="h-full w-full object-cover"
                                         src="{{ $avatar }}"
                                         alt="{{ $nameOfUser }}"
                                    />
                                </div>
                            @endif

                            <div class="profile-info">
                                <h5 class="text-purple">{{ $nameOfUser }}</h5>
                                <div class="email">{{ $username }}</div>
                            </div>
                        </div>
                    </x-slot:title>
                    <x-slot:toggler>
                        <x-moonshine::icon icon="chevron-up-down" color="white" />
                    </x-slot:toggler>

                    @if($logOutRoute)
                        <x-slot:footer>

                            <x-moonshine::form :action="$logOutRoute" :raw="true">
                                <x-moonshine::form.input type="hidden" name="_method" value="delete" />

                                <x-moonshine::form.button
                                    :raw="true"
                                    type="submit"
                                >
                                    <x-moonshine::icon icon="power" />
                                    {{ $translates['logout'] ?? 'Log out' }}
                                </x-moonshine::form.button>
                            </x-moonshine::form>
                        </x-slot:footer>
                    @endif

                    @if(is_iterable($menu))
                        <ul class="dropdown-menu">
                            @foreach($menu as $link)
                                <li class="dropdown-menu-item p-2">
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
@if($withBorder) </div> @endif
