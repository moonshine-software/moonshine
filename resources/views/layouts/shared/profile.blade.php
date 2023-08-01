<div class="mt-2 border-t border-dark-200">
    <div class="menu-profile">
        <a href="{{ to_page(MoonShine\Resources\MoonShineProfileResource::class) }}" class="menu-profile-main">
            <div class="menu-profile-photo">
                <img class="h-full w-full object-cover"
                     src="{{ auth()->user()->{config('moonshine.auth.fields.avatar', 'avatar')}
                        ? (Storage::url(auth()->user()->{config('moonshine.auth.fields.avatar', 'avatar')}))
                        : ("https://ui-avatars.com/api/?name=" . auth()->user()->{config('moonshine.auth.fields.name', 'name')}) }}"
                     alt="{{ auth()->user()->{config('moonshine.auth.fields.name', 'name')} }}"
                />
            </div>
            <div class="menu-profile-info">
                <h5 class="name">{{ auth()->user()->{config('moonshine.auth.fields.name', 'name')} }}</h5>
                <div class="email">{{ auth()->user()->{config('moonshine.auth.fields.username', 'email')} }}</div>
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
