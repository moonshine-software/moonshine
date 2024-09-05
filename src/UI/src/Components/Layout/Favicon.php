<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use MoonShine\UI\Components\MoonShineComponent;

final class Favicon extends MoonShineComponent
{
    protected string $view = 'moonshine::components.layout.favicon';

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
     *     web-manifest: string,
     * }  $assets
     * @return $this
     */
    public function assets(array $assets): self
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
        return [
            'assets' => $this->customAssets ?: [
                'apple-touch' => $this->getAssetManager()->getAsset('vendor/moonshine/apple-touch-icon.png'),
                '32' => $this->getAssetManager()->getAsset('vendor/moonshine/favicon-32x32.png'),
                '16' => $this->getAssetManager()->getAsset('vendor/moonshine/favicon-16x16.png'),
                'safari-pinned-tab' => $this->getAssetManager()->getAsset('vendor/moonshine/safari-pinned-tab.svg'),
                'web-manifest' => $this->getAssetManager()->getAsset('vendor/moonshine/site.webmanifest'),
            ],
            'bodyColor' => $this->bodyColor,
        ];
    }
}
