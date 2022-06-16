@extends("moonshine::layouts.login")

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-dark text-white py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div>
				@if(config("moonshine.logo"))
					<img class="mx-auto h-12 w-auto" src="{{ config("moonshine.logo") }}" alt="{{ config("moonshine.title") }}">
				@else
                    <svg class="fill-current mx-auto h-12 w-auto my-4 text-purple" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M17.75,4.09L15.22,6.03L16.13,9.09L13.5,7.28L10.87,9.09L11.78,6.03L9.25,4.09L12.44,4L13.5,1L14.56,4L17.75,4.09M21.25,11L19.61,12.25L20.2,14.23L18.5,13.06L16.8,14.23L17.39,12.25L15.75,11L17.81,10.95L18.5,9L19.19,10.95L21.25,11M18.97,15.95C19.8,15.87 20.69,17.05 20.16,17.8C19.84,18.25 19.5,18.67 19.08,19.07C15.17,23 8.84,23 4.94,19.07C1.03,15.17 1.03,8.83 4.94,4.93C5.34,4.53 5.76,4.17 6.21,3.85C6.96,3.32 8.14,4.21 8.06,5.04C7.79,7.9 8.75,10.87 10.95,13.06C13.14,15.26 16.1,16.22 18.97,15.95M17.33,17.97C14.5,17.81 11.7,16.64 9.53,14.5C7.36,12.31 6.2,9.5 6.04,6.68C3.23,9.82 3.34,14.64 6.35,17.66C9.37,20.67 14.19,20.78 17.33,17.97Z"></path>
                    </svg>
                @endif

                <div class="text-center">
                    @include('moonshine::shared.title', ['title' => trans('moonshine::ui.login.authorization')])
                </div>
            </div>

            <form class="mt-8" action="#" method="POST">
                {{ csrf_field() }}

                <input type="hidden" name="remember" value="true">

                <div class="rounded-md shadow-sm">
                    <div>
                        <input value="{{ old("email") }}" aria-label="{{ trans('moonshine::ui.login.email') }}" name="email" type="email" required
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 rounded-t-md focus:outline-none focus:shadow-outline-purple focus:border-purple focus:z-10 sm:text-sm sm:leading-5"
                               placeholder="{{ trans('moonshine::ui.login.email') }}">
                    </div>
                    <div class="-mt-px">
                        <input aria-label="{{ trans('moonshine::ui.login.password') }}" name="password" type="password" required
                               class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:shadow-outline-purple focus:border-purple focus:z-10 sm:text-sm sm:leading-5"
                               placeholder="{{ trans('moonshine::ui.login.password') }}">
                    </div>
                </div>

                @error("login")
                    <span class="flex items-center font-medium tracking-wide text-pink text-xs mt-1 ml-1">
                        {{ $message }}
                    </span>
                @enderror

                <div class="mt-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" class="form-checkbox h-4 w-4 text-pink transition duration-150 ease-in-out">
                        <label for="remember_me" class="ml-2 block text-sm leading-5">
                            {{ trans('moonshine::ui.login.remember_me') }}
                        </label>
                    </div>
                </div>

                <div class="mt-6">
                    @include('moonshine::base.form.shared.btn', [
                       'type' => 'submit',
                       'class' => 'w-full',
                       'name' => trans('moonshine::ui.login.login')
                   ])
                </div>
            </form>
        </div>
    </div>

@endsection