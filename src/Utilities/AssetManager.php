<?php

namespace Leeto\MoonShine\Utilities;

class AssetManager
{
    protected array $js = [];

    protected array $css = [];

    public function add(string|array $assets): void
    {
        if(is_array($assets)) {
            foreach ($assets as $asset) {
                $this->js[] = $asset;
                $this->css[] = $asset;
            }
        } else {
            $this->js[] = $assets;
            $this->css[] = $assets;
        }
    }

    public function js(): array
    {
        return $this->js;
    }

    public function css(): string
    {
        return '';
    }
}
