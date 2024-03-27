<?php

declare(strict_types=1);

namespace MoonShine\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Pages\Page;
use Throwable;

class MoonShineApplicationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @throws Throwable
     */
    public function boot(): void
    {
        moonshine()
            ->resources($this->resources())
            ->pages($this->pages())
            ->init();
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
        return [];
    }
}
