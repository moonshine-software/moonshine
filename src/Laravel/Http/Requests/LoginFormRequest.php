<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Validation\ValidationException;
use MoonShine\Laravel\MoonShineAuth;
use MoonShine\Laravel\Support\JWT;
use Random\RandomException;

class LoginFormRequest extends MoonShineFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return MoonShineAuth::getGuard()->guest();
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

    private function getCredentials(): array
    {
        return [
            moonshineConfig()->getUserField('username', 'email') => request(
                'username'
            ),
            moonshineConfig()->getUserField('password') => request()->input('password'),
        ];
    }

    /**
     * @throws RandomException
     */
    public function getAuthToken(): string
    {
        $this->ensureIsNotRateLimited();

        $user = MoonShineAuth::getModel()
            ?->newQuery()
            ->where(
                moonshineConfig()->getUserField('username', 'email'),
                request()->input('username')
            )
            ->first();

        if (is_null($user) || ! Hash::check(
            request()->input('password'),
            $user->{moonshineConfig()->getUserField('password')}
        )) {
            RateLimiter::hit($this->getThrottleKey());

            $this->validationException();
        }

        $token = (new JWT())->create((string) $user->getKey());

        RateLimiter::clear($this->getThrottleKey());

        return $token;
    }

    private function validationException(): void
    {
        throw ValidationException::withMessages([
            'username' => __('moonshine::auth.failed'),
        ]);
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

        if (! MoonShineAuth::getGuard()->attempt(
            $this->getCredentials(),
            $this->boolean('remember')
        )) {
            RateLimiter::hit($this->getThrottleKey());

            $this->validationException();
        }

        session()->regenerate();

        RateLimiter::clear($this->getThrottleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->getThrottleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->getThrottleKey());

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
    public function getThrottleKey(): string
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
                    moonshineConfig()->getUserField(
                        'username',
                        'email'
                    ) === 'email',
                    fn (Stringable $str): Stringable => $str->lower()
                )
                ->squish()
                ->value(),
        ]);
    }
}
