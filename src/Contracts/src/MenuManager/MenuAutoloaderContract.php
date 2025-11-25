<?php

declare(strict_types=1);

namespace MoonShine\Contracts\MenuManager;

/**
 * @phpstan-type PSMenuItem array{
 *     filler: class-string<MenuFillerContract>,
 *     canSee: string,
 *     position: null|int,
 * }
 *
 * @phpstan-type PSMenuGroup array{
 *     label: string,
 *     class: class-string<MenuFillerContract>,
 *     icon: string|null,
 *     canSee: string,
 *     translatable: bool,
 * }
 *
 * @phpstan-type PSMenuGroupWithItems array{group: PSMenuGroup, items: list<PSMenuItem>, position: null|int}
 * @phpstan-type PSMenu list<PSMenuItem|PSMenuGroupWithItems>
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
    public function resolve(?array $cached = null, bool $onlyIcons = false): array;
}
