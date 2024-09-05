<?php

declare(strict_types=1);

namespace MoonShine\Laravel\DependencyInjection;

use Composer\InstalledVersions;
use Illuminate\Support\Facades\Vite;
use MoonShine\Contracts\AssetManager\AssetResolverContract;

final class AssetResolver implements AssetResolverContract
{
    public function get(string $path): string
    {
        return asset($path);
    }

    public function getDev(string $path): ?string
    {
        return Vite::useBuildDirectory('vendor/moonshine')
            ->useHotFile($path)
            ->withEntryPoints(['resources/css/main.css', 'resources/js/app.js'])
            ->toHtml();
    }

    public function isDev(): bool
    {
        return app()->isLocal();
    }

    public function getVersion(): string
    {
        return InstalledVersions::getVersion('moonshine/moonshine');
    }

    public function getHotFile(): string
    {
        return MoonShine::UIPath('/dist/hot');
    }
}
