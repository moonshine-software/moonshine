<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use MoonShine\UI\Applies\AppliesRegister;
use Throwable;

class MoonShineApplicationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /** @var MoonShineConfigurator $configurator */
        $configurator = moonshineConfig();

        $this->configure(
            $configurator
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

        /** @var AppliesRegister $applyRegister */
        $applyRegister = appliesRegister();

        $this->appliesRegister(
            $applyRegister
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
     * @return array<class-string<PageContract>>
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
