<div class="w-full">
    @include('moonshine::base.form.shared.errors', ['errors' => $errors])

    <form
        action="{{ route(config('moonshine.route.prefix').'.profile.store') }}"
        class="bg-white dark:bg-darkblue shadow-md rounded-lg mb-4 text-white"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf

        <div class="border-b border-whiteblue dark:border-dark px-10 py-5">
            <div>
                <div class="px-4 py-5 sm:grid sm:grid-cols-4 sm:gap-2 sm:px-2">
                    <dt class="text-sm leading-5 font-medium text-gray-500 dark:text-white">
                        <label for="name">
                            @lang('moonshine::ui.resource.name')
                        </label>
                    </dt>

                    <dd class="mt-1 text-sm leading-5 text-gray-900 dark:text-white sm:mt-0 sm:col-span-3">
                        <input class="text-black dark:text-white bg-white dark:bg-darkblue focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 px-4 block w-full appearance-none leading-normal"
                               id="name"
                               placeholder="@lang('moonshine::ui.resource.name')"
                               name="name"
                               type="text"
                               value="{{ auth(config('moonshine.auth.guard'))->user()->name }}"
                        >
                    </dd>
                </div>
            </div>

            @error('name')
            @include('moonshine::base.form.shared.input-error', [
                'name' => 'name',
                'message' => $message
            ])
            @enderror
        </div>

        <div class="border-b border-whiteblue dark:border-dark px-10 py-5">
            <div>
                <div class="px-4 py-5 sm:grid sm:grid-cols-4 sm:gap-2 sm:px-2">
                    <dt class="text-sm leading-5 font-medium text-gray-500 dark:text-white">
                        <label for="email">
                            @lang('moonshine::ui.resource.email')
                        </label>
                    </dt>

                    <dd class="mt-1 text-sm leading-5 text-gray-900 dark:text-white sm:mt-0 sm:col-span-3">
                        <input class="text-black dark:text-white bg-white dark:bg-darkblue focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 px-4 block w-full appearance-none leading-normal"
                               id="email"
                               placeholder="@lang('moonshine::ui.resource.email')"
                               name="email"
                               type="email"
                               value="{{ auth(config('moonshine.auth.guard'))->user()->email }}"
                        >
                    </dd>
                </div>
            </div>

            @error('email')
            @include('moonshine::base.form.shared.input-error', [
                'name' => 'email',
                'message' => $message
            ])
            @enderror
        </div>

        <div class="border-b border-whiteblue dark:border-dark px-10 py-5">
            <div>
                <div class="px-4 py-5 sm:grid sm:grid-cols-4 sm:gap-2 sm:px-2">
                    <dt class="text-sm leading-5 font-medium text-gray-500 dark:text-white">
                        <label for="avatar">
                            @lang('moonshine::ui.resource.avatar')
                        </label>
                    </dt>

                    <dd class="mt-1 text-sm leading-5 text-gray-900 dark:text-white sm:mt-0 sm:col-span-3">
                        <div>
                            @if(auth(config('moonshine.auth.guard'))->user()->avatar)
                                <div x-data="{}" class="max-w-sm rounded overflow-hidden shadow-lg my-2">
                                    <img @click.stop="$dispatch('img-modal', {imgModal: true, imgModalSrc: '{{ Storage::url(auth(config('moonshine.auth.guard'))->user()->avatar) }}' })" class="w-full"
                                         src="{{ Storage::url(auth(config('moonshine.auth.guard'))->user()->avatar) }}">
                                </div>
                            @endif

                            <input class="text-black dark:text-white bg-white dark:bg-darkblue focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 px-4 block w-full appearance-none leading-normal" id="avatar" placeholder="Avatar" name="avatar" type="file">
                        </div>

                    </dd>
                </div>
            </div>

            @error('avatar')
            @include('moonshine::base.form.shared.input-error', [
                'name' => 'avatar',
                'message' => $message
            ])
            @enderror
        </div>

        <div class="border-b border-whiteblue dark:border-dark px-10 py-5">
            <div>
                <div class="px-4 py-5 sm:grid sm:grid-cols-4 sm:gap-2 sm:px-2">
                    <dt class="text-sm leading-5 font-medium text-gray-500 dark:text-white">
                        <label for="password">
                            @lang('moonshine::ui.resource.password')
                        </label>
                    </dt>

                    <dd class="mt-1 text-sm leading-5 text-gray-900 dark:text-white sm:mt-0 sm:col-span-3">
                        <input class="text-black dark:text-white bg-white dark:bg-darkblue focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 px-4 block w-full appearance-none leading-normal" autocomplete="new-password"
                               id="password"
                               placeholder="@lang('moonshine::ui.resource.password')" name="password" type="password" value="">

                    </dd>
                </div>
            </div>

            @error('password')
            @include('moonshine::base.form.shared.input-error', [
                'name' => 'password',
                'message' => $message
            ])
            @enderror
        </div>

        <div class="border-b border-whiteblue dark:border-dark px-10 py-5">
            <div>
                <div class="px-4 py-5 sm:grid sm:grid-cols-4 sm:gap-2 sm:px-2">
                    <dt class="text-sm leading-5 font-medium text-gray-500 dark:text-white">
                        <label for="password_repeat">
                            @lang('moonshine::ui.resource.repeat_password')
                        </label>
                    </dt>

                    <dd class="mt-1 text-sm leading-5 text-gray-900 dark:text-white sm:mt-0 sm:col-span-3">
                        <input class="text-black dark:text-white bg-white dark:bg-darkblue focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 px-4 block w-full appearance-none leading-normal" autocomplete="confirm-password" id="password_repeat" placeholder="@lang('moonshine::ui.resource.repeat_password')" name="password_repeat" type="password" value="">

                    </dd>
                </div>
            </div>

        </div>


        @if(config('moonshine.socialite'))
            <div class="border-b border-whiteblue dark:border-dark px-10 py-5">
                <div>
                    <div class="px-4 py-5 sm:grid sm:grid-cols-4 sm:gap-2 sm:px-2">
                        <dt class="text-sm leading-5 font-medium text-gray-500 dark:text-white">
                            <label>
                                @lang('moonshine::ui.resource.link_socialite')
                            </label>
                        </dt>

                        <dd class="mt-1 text-sm leading-5 text-gray-900 dark:text-white sm:mt-0 sm:col-span-3">
                            <div class="my-8 flex items-center justify-start space-x-4 space-y-2">
                                @foreach(config('moonshine.socialite') as $driver => $src)
                                    <a href="{{ route(config('moonshine.route.prefix') . '.socialite.redirect', $driver) }}">
                                        <img class="mx-auto h-12 w-auto" src="{{ $src }}" alt="{{ $driver }}">
                                    </a>
                                @endforeach
                            </div>

                            <div>
                                <div class="my-4">@lang('moonshine::ui.resource.linked_socialite')</div>

                                @foreach(auth(config('moonshine.auth.guard'))->user()->moonshineSocialites as $socials)
                                    <div class="my-4">{{ $socials->driver }} - {{ $socials->identity }}</div>
                                @endforeach
                            </div>
                        </dd>
                    </div>
                </div>

            </div>


        @endif

        <div class="px-10 py-10">
            @include('moonshine::base.form.shared.btn', [
                'type' => 'submit',
                'class' => '',
                'name' => trans('moonshine::ui.save')
            ])
        </div>
    </form>
</div>
