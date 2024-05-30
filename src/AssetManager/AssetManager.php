<?php

declare(strict_types=1);

namespace MoonShine\AssetManager;

use Closure;
use Composer\InstalledVersions;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\AssetManager\Contracts\AssetElement;
use MoonShine\MoonShine;

final class AssetManager implements Htmlable
{
    use Conditionable;

    /**
     * @var list<AssetElement>
     */
    private array $assets = [];

    private static ?Closure $assetUsing = null;

    private static ?Closure $viteDevUsing = null;

    /**
     * @param  Closure(string $path): void  $callback
     * @return void
     */
    public static function assetUsing(Closure $callback): void
    {
        self::$assetUsing = $callback;
    }

    public function asset(string $path): string
    {
        if(is_null(self::$assetUsing)) {
            return $path;
        }

        return value(self::$assetUsing, $path);
    }

    /**
     * @param  Closure(string $path): void  $callback
     * @return void
     */
    public static function viteDevUsing(Closure $callback): void
    {
        self::$viteDevUsing = $callback;
    }

    public function hasViteDevMode(): bool
    {
        return !is_null(self::$viteDevUsing);
    }

    public function viteDev(string $path): string
    {
        return value(self::$viteDevUsing, $path);
    }

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
                $this->isRunningHot() && $this->hasViteDevMode(),
                fn (AssetElements $assets) => $assets
                    ->push(
                        Raw::make($this->viteDev($this->hotFile()))
                    ),
            )
            ->withVersion($this->getVersion())
            ->toHtml();
    }

    private function isRunningHot(): bool
    {
        return moonshine()->isLocal() && is_file($this->hotFile());
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
