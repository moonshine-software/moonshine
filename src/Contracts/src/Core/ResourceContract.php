<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\MenuManager\MenuFillerContract;

/**
 * @template I of ResourceContract
 *
 * @mixin I
 */
interface ResourceContract extends
    HasCoreContract,
    MenuFillerContract,
    HasAssetsContract,
    HasUriKeyContract,
    StatefulContract
{
    public function getPages(): PagesContract;

    public function getRouter(): RouterContract;

    public function getTitle(): string;

    public function booted(): static;

    public function loaded(): static;
}
