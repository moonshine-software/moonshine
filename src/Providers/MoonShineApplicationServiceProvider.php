<?php

declare(strict_types=1);

namespace MoonShine\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Applies\AppliesRegister;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\MoonShineConfigurator;
use MoonShine\Pages\Page;
use Throwable;

class MoonShineApplicationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->configure(
            moonshineConfig()
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @throws Throwable
     */
    public function boot(): void
    {
        moonshine()
            ->resources($this->resources())
            ->pages($this->pages());

        $this->appliesRegister(
            appliesRegister()
        );
    }

    /**
     * @return array<class-string<ResourceContract>>
     */
    protected function resources(): array
    {
        return [];
    }

    /**
     * @return array<class-string<Page>>
     */
    protected function pages(): array
    {
        return [
            ...moonshineConfig()->getPages(),
        ];
    }

    protected function appliesRegister(AppliesRegister $register): AppliesRegister
    {
        return $register;
    }

    protected function configure(MoonShineConfigurator $config): MoonShineConfigurator
    {
        return $config;
    }
}
