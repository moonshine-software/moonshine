<header class="flex justify-between items-center py-4 px-6">
    <div class="flex justify-start items-center space-x-2">
        <button @click="sidebarOpen = true" class="focus:outline-none lg:hidden">
            <svg class="h-6 w-6 fill-current text-purple" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                      stroke-linejoin="round"></path>
            </svg>
        </button>

        <div>
            @section("header-inner")

            @show
        </div>
    </div>

    <div class="flex items-center">
        <button x-show="!darkMode" x-on:click="darkMode=true"
                class="relative w-10 h-10 focus:outline-none focus:shadow-outline">
            <svg class="h-6 w-6" viewBox="0 0 24 24">
                <path fill="currentColor"
                      d="M12 2A10 10 0 0 0 2 12A10 10 0 0 0 12 22A10 10 0 0 0 22 12A10 10 0 0 0 12 2M12 4A8 8 0 0 1 20 12A8 8 0 0 1 12 20V4Z"></path>
            </svg>
        </button>

        <button x-show="darkMode" x-on:click="darkMode=false"
                class="relative w-10 h-10 focus:outline-none focus:shadow-outline">
            <svg class="h-6 w-6" viewBox="0 0 24 24">
                <path fill="currentColor"
                      d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"></path>
            </svg>
        </button>

        @if(auth(config('moonshine.auth.guard'))->user()->unreadNotifications->isNotEmpty())
            <div x-data="{ dropdownOpen: false }" class="relative">
                <button @click="dropdownOpen = ! dropdownOpen"
                        class="relative block h-8 w-8 rounded-full overflow-hidden focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </button>

                <div x-show="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0 h-full w-full z-10"></div>

                <div x-show="dropdownOpen"
                     class="absolute right-0 w-96 mt-2 bg-white dark:bg-dark rounded-md overflow-hidden shadow-xl z-10"
                >
                    <div class="flex items-center justify-between p-5">
                        <span class="text-md font-bold">@lang('moonshine::ui.notifications.title')</span>
                        <a class="text-xs" href="{{ route(config('moonshine.route.prefix') . '.notifications.readAll') }}">@lang('moonshine::ui.notifications.mark_as_read_all')</a>
                    </div>

                    @foreach(auth(config('moonshine.auth.guard'))->user()->unreadNotifications as $notification)
                        <div class="flex flex-col p-8 shadow-md hover:shadow-lg rounded-2xl">
                            <div class="flex items-center justify-between space-x-4">
                                <p class="text-sm leading-none mt-1">
                                    {{ $notification->data['message'] }}
                                </p>

                                @if(isset($notification->data['button']['link']))
                                    @include('moonshine::shared.btn', [
                                        'href' => $notification->data['button']['link'],
                                        'title' => $notification->data['button']['label'],
                                        'filled' => true,
                                        'icon' => false
                                    ])
                                @endif

                                <a href="{{ route(config('moonshine.route.prefix') . '.notifications.read', $notification) }}"
                                   class="text-xs"
                                   title="@lang('moonshine::ui.notifications.mark_as_read')"
                                >
                                    x
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div x-data="{ dropdownOpen: false }" class="relative">
            <button @click="dropdownOpen = ! dropdownOpen"
                    class="relative block h-8 w-8 rounded-full overflow-hidden shadow focus:outline-none">
                <img class="h-full w-full object-cover"
                     src="{{ auth(config('moonshine.auth.guard'))->user()->avatar ? (Storage::url(auth(config('moonshine.auth.guard'))->user()->avatar)) : ("https://ui-avatars.com/api/?name=" . auth(config('moonshine.auth.guard'))->user()->name) }}"
                     alt="{{ auth(config('moonshine.auth.guard'))->user()->name }}">
            </button>

            <div x-show="dropdownOpen" @click="dropdownOpen = false" class="fixed inset-0 h-full w-full z-10"></div>

            <div x-show="dropdownOpen"
                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-dark rounded-md overflow-hidden shadow-xl z-10"
            >
                <a href="{{ route(config('moonshine.route.prefix') . '.custom_page', 'profile') }}"
                   class="block px-4 py-2 text-sm text-black dark:text-white dark:bg-purple hover:bg-purple hover:text-white">{{ trans('moonshine::ui.profile') }}</a>

                <a href="{{ route(config('moonshine.route.prefix') . '.logout') }}"
                   class="block px-4 py-2 text-sm text-black dark:text-white dark:bg-purple hover:bg-purple hover:text-white">{{ trans('moonshine::ui.login.logout') }}</a>
            </div>
        </div>
    </div>
</header>
