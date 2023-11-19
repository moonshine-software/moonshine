<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;
use MoonShine\Exceptions\AuthException;
use MoonShine\Models\MoonshineSocialite;
use RuntimeException;

class SocialiteController extends MoonShineController
{
    /**
     * @throws AuthException
     * @throws Exception
     */
    public function redirect(string $driver)
    {
        $this->ensureSocialiteIsInstalled();

        if (! $this->hasDriver($driver)) {
            throw new AuthException('Driver not found in config file');
        }

        return Socialite::driver($driver)
            ->redirect();
    }

    /**
     * @throws Exception
     */
    protected function ensureSocialiteIsInstalled(): void
    {
        if (class_exists(Socialite::class)) {
            return;
        }

        throw new RuntimeException(
            'Please install the Socialite: laravel/socialite'
        );
    }

    protected function hasDriver(string $driver): bool
    {
        return isset($this->drivers()[$driver]);
    }

    protected function drivers(): array
    {
        return config('moonshine.socialite', []);
    }

    /**
     * @throws AuthException
     * @throws Exception
     */
    public function callback(string $driver): RedirectResponse
    {
        $this->ensureSocialiteIsInstalled();

        if (! $this->hasDriver($driver)) {
            throw new AuthException('Driver not found in config file');
        }

        $socialiteUser = Socialite::driver($driver)->user();

        $account = MoonshineSocialite::query()
            ->where('driver', $driver)
            ->where('identity', $socialiteUser->getId())
            ->first();

        if ($this->auth()->check()) {
            return $this->bindAccount($socialiteUser, $driver, $account);
        }

        if (! $account) {
            $this->toast(
                __('moonshine::auth.failed'),
                'error'
            );

            return to_route('moonshine.login')->withErrors([
                'username' => __('moonshine::auth.failed'),
            ]);
        }

        $this->auth()->loginUsingId($account->moonshine_user_id);

        return to_route(
            moonShineIndexRoute()
        );
    }

    private function bindAccount(
        User $socialiteUser,
        string $driver,
        ?MoonshineSocialite $account
    ): RedirectResponse {
        if ($account instanceof MoonshineSocialite) {
            $this->toast(
                __('moonshine::auth.socialite.link_exists')
            );
        } else {
            MoonshineSocialite::query()->create([
                'moonshine_user_id' => $this->auth()->id(),
                'driver' => $driver,
                'identity' => $socialiteUser->getId(),
            ]);

            $this->toast(
                __('moonshine::auth.socialite.link_success'),
                'success'
            );
        }

        return to_route(
            'moonshine.custom_page',
            'profile'
        );
    }
}
