<?php

declare(strict_types=1);

namespace MoonShine\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;
use Laravel\Socialite\Contracts\User;
use Laravel\Socialite\Facades\Socialite;
use MoonShine\Exceptions\AuthException;
use MoonShine\Models\MoonshineSocialite;
use MoonShine\MoonShineAuth;
use RuntimeException;

class SocialiteController extends BaseController
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

        if (MoonShineAuth::guard()->check()) {
            return $this->bindAccount($socialiteUser, $driver, $account);
        }

        if (! $account) {
            return to_route('moonshine.login')->withErrors([
                'username' => __('moonshine::auth.failed'),
            ])->with('alert', __('moonshine::auth.failed'));
        }

        MoonShineAuth::guard()
            ->loginUsingId($account->moonshine_user_id);

        return to_route('moonshine.index');
    }

    private function bindAccount(User $socialiteUser, string $driver, ?MoonshineSocialite $account): RedirectResponse
    {
        if ($account) {
            session()->flash('alert', __('moonshine::auth.socialite.link_exists'));
        } else {
            MoonshineSocialite::query()->create([
                'moonshine_user_id' => MoonShineAuth::guard()->id(),
                'driver' => $driver,
                'identity' => $socialiteUser->getId(),
            ]);

            session()->flash('success', __('moonshine::auth.socialite.link_success'));
        }

        return to_route(
            'moonshine.custom_page',
            'profile'
        );
    }

    protected function drivers(): array
    {
        return config('moonshine.socialite', []);
    }

    protected function hasDriver(string $driver): bool
    {
        return isset($this->drivers()[$driver]);
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
}
