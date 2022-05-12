@extends("moonshine::layouts.login")

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div>
				@if(config("moonshine.logo"))
					<img class="mx-auto h-12 w-auto" src="{{ config("moonshine.logo") }}" alt="{{ config("moonshine.title") }}">
				@else
                    <svg class="fill-current mx-auto h-12 w-auto text-purple" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"></path>
                    </svg>
                @endif

                <h2 class="mt-6 text-center text-3xl leading-9 font-extrabold text-gray-900">
                    {{ trans('moonshine::ui.login.authorization') }}
                </h2>
            </div>

            <form class="mt-8" action="#" method="POST">
                {{ csrf_field() }}

                <input type="hidden" name="remember" value="true">

                <div class="rounded-md shadow-sm">
                    <div>
                        <input value="{{ old("email") }}" aria-label="{{ trans('moonshine::ui.login.email') }}" name="email" type="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:shadow-outline-purple focus:border-purple focus:z-10 sm:text-sm sm:leading-5"
                               placeholder="{{ trans('moonshine::ui.login.email') }}">
                    </div>
                    <div class="-mt-px">
                        <input aria-label="{{ trans('moonshine::ui.login.password') }}" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:shadow-outline-purple focus:border-purple focus:z-10 sm:text-sm sm:leading-5"
                               placeholder="{{ trans('moonshine::ui.login.password') }}">
                    </div>
                </div>

                @error("login")
                    <span class="flex items-center font-medium tracking-wide text-red-500 text-xs mt-1 ml-1">
                        {{ $message }}
                    </span>
                @enderror

                <div class="mt-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" class="form-checkbox h-4 w-4 text-pink transition duration-150 ease-in-out">
                        <label for="remember_me" class="ml-2 block text-sm leading-5 text-gray-900">
                            {{ trans('moonshine::ui.login.rememberme') }}
                        </label>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-pink hover:bg-pink focus:outline-none focus:border-pink focus:shadow-outline-pink active:bg-pink transition duration-150 ease-in-out">
                      <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-pink group-hover:text-pink transition ease-in-out duration-150" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                      </span>
                        {{ trans('moonshine::ui.login.login') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection