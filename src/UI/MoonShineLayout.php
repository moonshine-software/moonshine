<?php

declare(strict_types=1);

namespace MoonShine\UI;

use MoonShine\AssetManager\AssetManager;
use MoonShine\AssetManager\Css;
use MoonShine\AssetManager\Js;
use MoonShine\ColorManager\ColorManager;
use MoonShine\Core\Contracts\PageContract;
use MoonShine\MenuManager\MenuManager;
use MoonShine\UI\Components\Layout\{LayoutBuilder};

abstract class MoonShineLayout
{
    public function __construct(
        private readonly PageContract $page,
        private readonly AssetManager $assetManager,
        private readonly ColorManager $colorManager,
        private readonly MenuManager $menuManager,
    ) {
        $this->assetManager->add(
            $this->assets()
        );

        $this->menuManager->add(
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

    protected function colors(ColorManager $colorManager): void
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
