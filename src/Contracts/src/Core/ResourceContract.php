<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\MenuManager\MenuFillerContract;

/**
 * @template TPage of PageContract = PageContract
 * @template TCore of CoreContract = CoreContract
 *
 * @extends HasCoreContract<TCore>
 */
interface ResourceContract extends
    HasCoreContract,
    MenuFillerContract,
    HasAssetsContract,
    HasUriKeyContract,
    StatefulContract
{
    /**
     * @return PagesContract<TPage>
     */
    public function getPages(): PagesContract;

    public function getRouter(): RouterContract;

    public function getTitle(): string;

    public function booted(): static;

    public function loaded(): static;
}
