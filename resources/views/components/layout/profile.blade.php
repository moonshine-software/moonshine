@props([
    'route',
    'logOutRoute',
    'avatar',
    'name' => '',
    'username' => '',
    'withBorder' => false,
    'before',
    'after',
])
@if($withBorder) <div {{ $attributes->merge(['class' => 'mt-2 border-t border-dark-200']) }}> @endif
    {{ $before ?? '' }}

    @if(isset($slot) && $slot->isNotEmpty())
        {{ $slot }}
    @else
        <div class="menu-profile">
            <a href="{{ $route }}"
               class="menu-profile-main"
            >
                <div class="menu-profile-photo">
                    <img class="h-full w-full object-cover"
                         src="{{ $avatar }}"
                         alt="{{ $nameOfUser }}"
                    />
                </div>
                <div class="menu-profile-info">
                    <h5 class="name">{{ $nameOfUser }}</h5>
                    <div class="email">{{ $username }}</div>
                </div>
            </a>

            <a href="{{ $logOutRoute }}"
               class="menu-profile-exit"
               title="Logout"
            >
                <x-moonshine::icon
                    icon="heroicons.power"
                    color="gray"
                    size="6"
                />
            </a>
        </div>
    @endif

    {{ $after ?? '' }}
@if($withBorder) </div> @endif
