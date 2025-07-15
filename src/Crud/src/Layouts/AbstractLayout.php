<?php

declare(strict_types=1);

namespace MoonShine\Crud\Layouts;

use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;
use MoonShine\Contracts\AssetManager\AssetElementContract;
use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\MenuManager\MenuAutoloaderContract;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\Contracts\UI\LayoutContract;
use MoonShine\UI\Components\Layout\{Layout};

/**
 * @template TCore of CoreContract
 */
abstract class AbstractLayout implements LayoutContract
{
    protected bool $booted = false;

    protected PageContract $page;

    /**
     * @param  TCore  $core
     */
    public function __construct(
        protected readonly CoreContract $core,
        protected readonly AssetManagerContract $assetManager,
        protected readonly ColorManagerContract $colorManager,
        protected readonly MenuManagerContract $menuManager,
        protected readonly MenuAutoloaderContract $menuAutoloader,
    ) {
        $this->getAssetManager()->add(
            $this->assets(),
        );

        $this->getMenuManager()->add(
            $this->menu(),
        );

        $this->colors(
            $this->colorManager,
        );

        $this->booted();
    }

    protected function onBoot(): void
    {
        //
    }

    public function booted(): static
    {
        if ($this->booted) {
            return $this;
        }

        $this->onBoot();

        $this->booted = true;

        return $this;
    }

    public function setPage(PageContract $page): static
    {
        $this->page = $page;

        return $this;
    }

    protected function getPage(): PageContract
    {
        return $this->page;
    }

    /**
     * @return TCore
     */
    protected function getCore(): CoreContract
    {
        return $this->core;
    }

    protected function getAssetManager(): AssetManagerContract
    {
        return $this->assetManager;
    }

    protected function getColorManager(): ColorManagerContract
    {
        return $this->colorManager;
    }

    protected function getMenuManager(): MenuManagerContract
    {
        return $this->menuManager;
    }

    protected function colors(ColorManagerContract $colorManager): void
    {
        //
    }

    /**
     * @return non-empty-array<AssetElementContract>
     */
    protected function assets(): array
    {
        return [
            $this->getMainThemeJs(),
            $this->getMainThemeCss(),
        ];
    }

    protected function getMainThemeJs(): Js
    {
        return Js::make('/vendor/moonshine/assets/app.js')->defer();
    }

    protected function getMainThemeCss(): Css
    {
        return Css::make('/vendor/moonshine/assets/main.css')->defer();
    }

    protected function getCompactThemeCss(): Css
    {
        return Css::make('/vendor/moonshine/assets/minimalistic.css')->defer();
    }

    /**
     * @return list<MenuElementContract>
     */
    protected function menu(): array
    {
        return [];
    }

    /**
     * @return list<MenuElementContract>
     */
    protected function autoloadMenu(): array
    {
        $data = $this->getCore()->getOptimizer()->hasType(MenuElementContract::class)
            ? $this->getCore()->getOptimizer()->getType(MenuElementContract::class)
            : null;

        return $this->menuAutoloader->resolve($data);
    }

    abstract public function build(): Layout;
}
