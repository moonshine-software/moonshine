<?php

declare(strict_types=1);

namespace MoonShine\Contracts\MenuManager;

use Closure;
use MoonShine\Contracts\Core\HasCanSeeContract;
use MoonShine\Contracts\Core\HasCoreContract;
use MoonShine\Contracts\UI\HasComponentAttributesContract;
use MoonShine\Contracts\UI\HasIconContract;
use MoonShine\Contracts\UI\HasLabelContract;

interface MenuElementContract extends
    HasCanSeeContract,
    HasIconContract,
    HasCoreContract,
    HasLabelContract,
    HasComponentAttributesContract
{
    public function isActive(): bool;

    /** @param null|bool|Closure(static $ctx): bool $condition */
    public function topMode(Closure|bool|null $condition = true): static;

    /** @param null|bool|Closure(static $ctx): bool $condition */
    public function onlyIcon(Closure|bool|null $condition = true): static;
}
