<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Core\Contracts\ConfiguratorContract;
use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Laravel\MoonShineConfigurator;
use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Applies\AppliesRegister;
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
