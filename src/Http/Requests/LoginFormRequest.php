<?php

declare(strict_types=1);

namespace MoonShine\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Validation\ValidationException;
use MoonShine\MoonShineAuth;
use MoonShine\MoonShineRequest;

class LoginFormRequest extends MoonShineRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return MoonShineAuth::guard()->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'username' => ['required'],
            'password' => ['required'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'username' => request()->str('username')
                ->when(
                    config('moonshine.auth.fields.username', 'email') === 'email',
                    fn (Stringable $str) => $str->lower()
                )
                ->squish()
                ->value(),
        ]);
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = [
            config('moonshine.auth.fields.username', 'email') => $this->get('username'),
            config('moonshine.auth.fields.password', 'password') => $this->get('password'),
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
     * @return void
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
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            str($this->input('username').'|'.$this->ip())
                ->lower()
        );
    }
}
