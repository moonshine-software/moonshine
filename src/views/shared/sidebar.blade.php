<div :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false"
     class="fixed z-20 inset-0 bg-black opacity-50 transition-opacity lg:hidden"></div>

<div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
     class="fixed z-30 inset-y-0 left-0 w-64 transition duration-300
     transform bg-gray-200 dark:bg-black overflow-y-auto lg:translate-x-0 lg:static lg:inset-0"
>
    <div class="flex items-center justify-center mt-8">
        <div class="flex items-center">
            <a href="{{ route(config('moonshine.route.prefix') . '.index') }}">
				@if(config("moonshine.logo"))
					<img class="rounded-full h-10 mb-3 mx-auto" src="{{ config("moonshine.logo") }}" alt="{{ config("moonshine.title") }}">
				@else
                    <svg class="fill-current h-10 text-purple" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"></path>
                    </svg>
				@endif
            </a>
        </div>
    </div>

    @section("sidebar-inner")
    @show

    <x-moonshine::menu-component />
</div>