<?php

declare(strict_types=1);

namespace MoonShine\Contracts\MenuManager;

/**
 * @internal
 * @phpstan-type PSMenuItem array{
 *      filler: class-string<MenuFillerContract>,
 *      canSee: null|string,
 *      position: null|int,
 *  }
 *
 * @phpstan-type PSMenuGroup array{
 *       label: string,
 *       class: class-string<MenuFillerContract>,
 *       icon: string|null,
 *       canSee: null|string,
 *       translatable: bool,
 *   }
 *
 * @phpstan-type PSMenu array{
 *      string,
 *      PSMenuItem|array{
 *           group: PSMenuGroup,
 *           items: list<PSMenuItem>,
 *           position: null|int,
 *      }
 *  }
 */
interface MenuAutoloaderContract
{
    /**
     * @return PSMenu
     */
    public function toArray(): array;

    /**
     * @param  PSMenu|null  $cached
     *
     * @return MenuElementContract[]
     */
    public function resolve(?array $cached = null): array;
}
