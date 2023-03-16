<a href="{{ route('moonshine.index') }}" class="block" rel="home">
    @if(config('moonshine.logo'))
        <img class="h-14 xl:block"
             src="{{ config('moonshine.logo') }}"
             alt="{{ config('moonshine.title') }}"
        />
    @else
        <img src="{{ asset('vendor/moonshine/logo.svg') }}"
             class="hidden h-14 xl:block"
             :class="minimizedMenu && '!hidden'"
             alt="{{ config('moonshine.title') }}"
        />
        <img src="{{ asset('vendor/moonshine/logo-small.svg') }}"
             class="block h-8 lg:h-10 xl:hidden"
             :class="minimizedMenu && '!block'"
             alt="{{ config('moonshine.title') }}"
        />
    @endif
</a>
