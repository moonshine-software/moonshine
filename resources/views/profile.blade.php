<x-moonshine::form
    action="{{ route('moonshine.profile.store') }}"
    method="POST"
    enctype="multipart/form-data"
    :errors="$errors"
>
    <div class="form-flex-col">
        <x-moonshine::form.input-wrapper
            name="name"
            label="{{ trans('moonshine::ui.resource.name') }}"
        >
            <x-moonshine::form.input
                id="name"
                type="text"
                name="name"
                @class(['form-invalid' => $errors->has('name')])
                placeholder="{{ trans('moonshine::ui.resource.name') }}"
                value="{{ old('name', auth()->user()
                                ->{config('moonshine.auth.fields.name', 'name')}) }}"
            />
        </x-moonshine::form.input-wrapper>

        <x-moonshine::form.input-wrapper
            name="username"
            label="{{ trans('moonshine::ui.login.username') }}"
            required
        >
            <x-moonshine::form.input
                id="username"
                type="text"
                name="username"
                @class(['form-invalid' => $errors->has('username')])
                placeholder="{{ trans('moonshine::ui.login.username') }}"
                autocomplete="username"
                required
                value="{{ old('username', auth()->user()
                        ->{config('moonshine.auth.fields.username', 'email')}) }}"
            />
        </x-moonshine::form.input-wrapper>

        <x-moonshine::form.input-wrapper
            name="password"
            label="{{ trans('moonshine::ui.resource.password') }}"
        >
            <x-moonshine::form.input
                id="password"
                type="password"
                name="password"
                @class(['form-invalid' => $errors->has('password')])
                placeholder="{{ trans('moonshine::ui.resource.password') }}"
                autocomplete="new-password"
            />
        </x-moonshine::form.input-wrapper>

        <x-moonshine::form.input-wrapper
            name="password_repeat"
            label="{{ trans('moonshine::ui.resource.repeat_password') }}"
        >
            <x-moonshine::form.input
                id="password_repeat"
                type="password"
                name="password_repeat"
                @class(['form-invalid' => $errors->has('password_repeat')])
                placeholder="{{ trans('moonshine::ui.resource.repeat_password') }}"
                autocomplete="confirm-password"
            />
        </x-moonshine::form.input-wrapper>

        @if(config('moonshine.auth.fields.avatar', true))
            <x-moonshine::form.input-wrapper
                name="avatar"
                label="{{ trans('moonshine::ui.resource.avatar') }}"
            >
                <x-moonshine::form.file
                    name="avatar"
                    id="avatar"
                    @class(['form-invalid' => $errors->has('avatar')])
                    placeholder="{{ trans('moonshine::ui.resource.avatar') }}"
                    :files="[auth()->user()
                            ->{config('moonshine.auth.fields.avatar', 'avatar')} ?? null]"
                    dir="moonshine_users"
                    :path="Storage::disk('public')->url('/')"
                    :removable="true"
                    :imageable="true"
                />
            </x-moonshine::form.input-wrapper>
        @endif
    </div>

    <div class="my-4"></div>

    @include('moonshine::ui.social-auth', [
        'title' => trans('moonshine::ui.resource.link_socialite'),
        'attached' => true
    ])

    <x-slot:button type="submit">
        {{ trans('moonshine::ui.save') }}
    </x-slot:button>
</x-moonshine::form>

