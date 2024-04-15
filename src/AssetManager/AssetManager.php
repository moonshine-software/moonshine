<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use Closure;
use Composer\InstalledVersions;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\MoonShine;

final class AssetManager implements Htmlable
{
    use Conditionable;

    /**
     * @var list<AssetElement>
     */
    private array $assets = [];

    /**
     * @parent list<AssetElement> $assets
     */
    public function add(AssetElement|array $assets): self
    {
        $this->assets = array_unique(
            array_merge(
                $this->assets,
                is_array($assets) ? $assets : [$assets]
            )
        );

        return $this;
    }

    public function getAssets(): AssetElements
    {
        return AssetElements::make($this->assets);
    }

    public function toHtml(): string
    {
        return $this->getAssets()
            ->ensure(AssetElement::class)
            ->when(
                $this->isRunningHot(),
                fn (AssetElements $assets) => $assets
                    ->push(
                        Raw::make(
                            Vite::useBuildDirectory('vendor/moonshine')
                                ->useHotFile($this->hotFile())
                                ->withEntryPoints(['resources/css/main.css', 'resources/js/app.js'])
                                ->toHtml()
                        )
                    ),
            )
            ->withVersion($this->getVersion())
            ->toHtml();
    }

    private function isRunningHot(): bool
    {
        return app()->isLocal() && is_file($this->hotFile());
    }

    private function hotFile(): string
    {
        return MoonShine::path('/public') . '/hot';
    }

    private function getVersion(): string
    {
        return InstalledVersions::getVersion('moonshine/moonshine');
    }
}
