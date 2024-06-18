<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Validation\ValidationException;
use MoonShine\MoonShineAuth;

class LoginFormRequest extends MoonShineFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return MoonShineAuth::guard()->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{username: string[], password: string[]}
     */
    public function rules(): array
    {
        return [
            'username' => ['required'],
            'password' => ['required'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = [
            config('moonshine.auth.fields.username', 'email') => request()->input('username'),
            config('moonshine.auth.fields.password', 'password') => request()->input('password'),
        ];

        if (! MoonShineAuth::guard()->attempt(
            $credentials,
            $this->boolean('remember')
        )) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => __('moonshine::auth.failed'),
            ]);
        }

        session()->regenerate();

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('moonshine::auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            str($this->input('username') . '|' . $this->ip())
                ->lower()
        );
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'username' => request()->str('username')
                ->when(
                    config(
                        'moonshine.auth.fields.username',
                        'email'
                    ) === 'email',
                    fn (Stringable $str): Stringable => $str->lower()
                )
                ->squish()
                ->value(),
        ]);
    }
}
