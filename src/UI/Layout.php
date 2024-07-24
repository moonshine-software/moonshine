<?php

declare(strict_types=1);

namespace MoonShine\UI;

use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;
use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\ColorManager\ColorManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\Contracts\UI\LayoutContract;
use MoonShine\UI\Components\Layout\{LayoutBuilder};

abstract class Layout implements LayoutContract
{
    public function __construct(
        protected readonly CoreContract $core,
        protected readonly PageContract $page,
        protected readonly AssetManagerContract $assetManager,
        protected readonly ColorManagerContract $colorManager,
        protected readonly MenuManagerContract $menuManager,
    ) {
        $this->getAssetManager()->add(
            $this->assets()
        );

        $this->getMenuManager()->add(
            $this->menu()
        );

        $this->colors(
            $this->colorManager
        );
    }

    protected function getPage(): PageContract
    {
        return $this->page;
    }

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

    protected function assets(): array
    {
        return [
            Js::make('/vendor/moonshine/assets/app.js')->defer(),
            Css::make('/vendor/moonshine/assets/main.css')->defer(),
        ];
    }

    protected function menu(): array
    {
        return [];
    }

    abstract public function build(): LayoutBuilder;
}
