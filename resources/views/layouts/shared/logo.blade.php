<a href="{{ $home_route ?? moonshineRouter()->home() }}" class="block" rel="home">
    <img src="{{ $logo ?? asset(config('moonshine.logo') ?? 'vendor/moonshine/logo.svg') }}"
         class="hidden h-14 xl:block"
         :class="minimizedMenu && '!hidden'"
         alt="{{ config('moonshine.title') }}"
    />

    <img src="{{ $logoSmall ?? asset(config('moonshine.logo_small') ?? 'vendor/moonshine/logo-small.svg') }}"
         class="block h-8 lg:h-10 xl:hidden"
         :class="minimizedMenu && '!block'"
         alt="{{ config('moonshine.title') }}"
    />
</a>
