@extends("moonshine::layouts.login")

@section('content')
    <div class="authentication">
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

            {!! $form() !!}

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
