<?php

declare(strict_types=1);

namespace MoonShine\Theme;

use Closure;
use Composer\InstalledVersions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\MoonShine;

class AssetManager
{
    use Conditionable;

    private array $assets = [];

    private string $mainJs = '/vendor/moonshine/assets/app.js';

    private string $mainCss = '/vendor/moonshine/assets/main.css';

    private ?Closure $lazy = null;

    private bool $extracted = false;

    public function lazyAssign(Closure $closure): self
    {
        $this->lazy = $closure;

        return $this;
    }

    public function mainCss(string $path): self
    {
        $this->mainCss = $path;

        return $this;
    }

    protected function getMainCss(): string
    {
        return $this->mainCss;
    }

    protected function getMainJs(): string
    {
        return $this->mainJs;
    }

    public function add(string|array|Closure $assets): void
    {
        if (is_closure($assets)) {
            $this->lazyAssign($assets);
        } else {
            $this->assets = array_unique(
                array_merge(
                    $this->assets,
                    is_array($assets) ? $assets : [$assets]
                )
            );
        }
    }

    public function getAssets(): array
    {
        return $this->assets;
    }

    public function js(): string
    {
        return collect($this->assets)
            ->when(! $this->isRunningHot(), fn (Collection $assets) => $assets->push($this->getMainJs()))
            ->filter(
                fn ($asset): int|bool => str_contains((string) $asset, '.js')
            )
            ->map(
                fn ($asset): string => "<script defer src='" . asset(
                    $asset
                ) . (str_contains((string) $asset, '?') ? '&' : '?') . "v={$this->getVersion()}'></script>"
            )->implode(PHP_EOL);
    }

    public function css(): string
    {
        return collect($this->assets)
            ->when(! $this->isRunningHot(), fn (Collection $assets) => $assets->prepend($this->getMainCss()))
            ->filter(
                fn ($asset): int|bool => str_contains((string) $asset, '.css')
            )
            ->map(
                fn ($asset): string => "<link href='" . asset(
                    $asset
                ) . (str_contains((string) $asset, '?') ? '&' : '?') . "v={$this->getVersion()}' rel='stylesheet'>"
            )->implode(PHP_EOL);
    }

    private function lazyExtract(): void
    {
        if (! $this->extracted) {
            $this->when(
                value($this->lazy, moonshineRequest()),
                fn (self $class, array $data) => $class
                    ->when(
                        isset($data['css']) && $data['css'] !== '',
                        static fn (AssetManager $assets): AssetManager => $assets->mainCss($data['css'])
                    )->when(
                        isset($data['assets']) && $data['assets'] !== [],
                        static fn (AssetManager $assets) => $assets->add($data['assets'])
                    )
            );
        }

        $this->extracted = true;
    }

    public function toHtml(): string
    {
        $this->lazyExtract();

        if ($this->isRunningHot()) {
            $vendorAssets = Vite::useBuildDirectory('vendor/moonshine')
                ->useHotFile($this->hotFile())
                ->withEntryPoints(['resources/css/main.css', 'resources/js/app.js'])
                ->toHtml();
        }

        return implode(PHP_EOL, [$this->js(), $vendorAssets ?? '', $this->css()]);
    }

    private function isRunningHot(): bool
    {
        return app()->isLocal() && is_file($this->hotFile());
    }

    private function hotFile(): string
    {
        return MoonShine::path('/public') . '/hot';
    }

    public function getVersion(): string
    {
        return InstalledVersions::getVersion('moonshine/moonshine');
    }
}
