<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\Core\DependencyInjection\RouterContract;

/**
 * @template-covariant I of ResourceContract

 * @mixin I
 */
interface ResourceContract
{
    public function getPages(): PagesContract;

    public function getRouter(): RouterContract;

    public function getUriKey(): string;

    public function getUrl(): string;

    public function getTitle(): string;

    public function getAssets(): array;

    public function booted(): static;

    public function loaded(): static;

    public function flushState(): void;
}
