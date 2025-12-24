<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\MoonShineComponent;

final class Favicon extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.favicon';

    /**
     * @param  array{
     *     apple-touch?: string,
     *     32?: string,
     *     16?: string,
     *     safari-pinned-tab?: string,
     * }  $customAssets
     */
    public function __construct(
        private array $customAssets = [],
        private ?string $bodyColor = null
    ) {
        parent::__construct();
    }

    /**
     * @param  array{
     *     apple-touch: string,
     *     32: string,
     *     16: string,
     *     safari-pinned-tab: string,
     * }  $assets
     */
    public function customAssets(array $assets): self
    {
        $this->customAssets = $assets;

        return $this;
    }

    public function bodyColor(string $color): self
    {
        $this->bodyColor = $color;

        return $this;
    }

    protected function viewData(): array
    {
        $favicons = $this->getCore()->getConfig()->get('favicons');

        return [
            'assets' => $this->customAssets ?: array_filter([
                'apple-touch' => $this->getAssetManager()->getAsset($favicons['apple-touch'] ?? '/vendor/moonshine/apple-touch-icon.png'),
                '32' => $this->getAssetManager()->getAsset($favicons['32'] ?? '/vendor/moonshine/favicon-32x32.png'),
                '16' => $this->getAssetManager()->getAsset($favicons['16'] ?? '/vendor/moonshine/favicon-16x16.png'),
                'safari-pinned-tab' => $this->getAssetManager()->getAsset($favicons['safari-pinned-tab'] ?? '/vendor/moonshine/safari-pinned-tab.svg'),
            ]),
            'bodyColor' => $this->bodyColor,
        ];
    }
}
