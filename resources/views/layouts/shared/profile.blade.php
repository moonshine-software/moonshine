<!-- User menu -->
<div class="mt-2 border-t border-dark-200">
    <div class="menu-profile">
        <a href="{{ route('moonshine.custom_page', 'profile') }}" class="menu-profile-main">
            <div class="menu-profile-photo">
                <img class="h-full w-full object-cover"
                     src="{{ auth(config('moonshine.auth.guard'))->user()->avatar ? (Storage::url(auth(config('moonshine.auth.guard'))->user()->avatar)) : ("https://ui-avatars.com/api/?name=" . auth(config('moonshine.auth.guard'))->user()->name) }}"
                     alt="{{ auth(config('moonshine.auth.guard'))->user()->name }}"
                />
            </div>
            <div class="menu-profile-info">
                <h5 class="name">{{ auth(config('moonshine.auth.guard'))->user()->name }}</h5>
                <div class="email">{{ auth(config('moonshine.auth.guard'))->user()->email }}</div>
            </div>
        </a>

        <a href="{{ route('moonshine.logout') }}" class="menu-profile-exit" title="Logout">
            <x-moonshine::icon
                icon="heroicons.power"
                color="gray"
                size="6"
            />
        </a>
    </div>
</div>
