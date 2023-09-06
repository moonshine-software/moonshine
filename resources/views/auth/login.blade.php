@extends("moonshine::layouts.login")

@section('content')
    <div class="authentication" xmlns:x-slot="http://www.w3.org/1999/html">
        <div class="authentication-logo">
            <a href="/" rel="home">
                <img class="h-16"
                     src="{{ config('moonshine.logo') ?: asset('vendor/moonshine/logo.svg') }}"
                     alt="{{ config('moonshine.title') }}"
                >
            </a>
        </div>
        <div class="authentication-content">
            <div class="authentication-header">
                <h1 class="title">@lang('moonshine::ui.login.title', ['moonshine_title' => config('moonshine.title')])</h1>
                <p class="description">
                    @lang('moonshine::ui.login.description')
                </p>
            </div>

            <x-moonshine::form
                class="authentication-form"
                action="{{ route('moonshine.authenticate') }}"
                method="POST"
                :errors="false"
            >
                <div class="form-flex-col">
                    <x-moonshine::form.input-wrapper
                        name="username"
                        label="{{ trans('moonshine::ui.login.username') }}"
                        required
                    >
                        <x-moonshine::form.input
                            id="username"
                            type="username"
                            name="username"
                            @class(['form-invalid' => $errors->has('username')])
                            placeholder="{{ trans('moonshine::ui.login.username') }}"
                            required
                            autofocus
                            value="{{ old('username') }}"
                            autocomplete="username"
                        />
                    </x-moonshine::form.input-wrapper>

                    <x-moonshine::form.input-wrapper
                        name="password"
                        label="{{ trans('moonshine::ui.login.password') }}"
                        required
                        autocomplete="current-password"
                    >
                        <x-moonshine::form.input
                            id="password"
                            type="password"
                            name="password"
                            @class(['form-invalid' => $errors->has('password')])
                            placeholder="{{ trans('moonshine::ui.login.password') }}"
                            required
                        />
                    </x-moonshine::form.input-wrapper>

                    <x-moonshine::form.input-wrapper
                        name="remember_me"
                        class="form-group-inline"
                        label="{{ trans('moonshine::ui.login.remember_me') }}"
                        :beforeLabel="true"
                    >
                        <x-moonshine::form.input
                            type="hidden"
                            name="remember"
                            value="0"
                        />

                        <x-moonshine::form.input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            value="1"
                        />
                    </x-moonshine::form.input-wrapper>
                </div>

                <x-slot:button type="submit" class="btn-lg w-full">
                    {{ trans('moonshine::ui.login.login') }}
                </x-slot:button>
            </x-moonshine::form>

            <p class="text-center text-2xs">
                {!! config('moonshine.auth.footer', '') !!}
            </p>

            <div class="authentication-footer">
                @include('moonshine::ui.social-auth', [
                    'title' => trans('moonshine::ui.login.or_socials')
                ])
            </div>
        </div>
    </div>
@endsection
