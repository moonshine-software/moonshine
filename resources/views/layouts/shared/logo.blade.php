<a href="{{ $home_route ?? route(moonShineIndexRoute()) }}" class="block" rel="home">
        <img src="{{ config('moonshine.logo') ?: asset('vendor/moonshine/logo.svg') }}"
             class="hidden h-14 xl:block"
             :class="minimizedMenu && '!hidden'"
             alt="{{ config('moonshine.title') }}"
        />
        <img src="{{ config('moonshine.logo_small') ?: asset('vendor/moonshine/logo-small.svg') }}"
             class="block h-8 lg:h-10 xl:hidden"
             :class="minimizedMenu && '!block'"
             alt="{{ config('moonshine.title') }}"
        />
</a>
