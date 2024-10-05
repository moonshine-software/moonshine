<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\MenuManager\MenuFillerContract;
use MoonShine\Contracts\UI\LayoutContract;
use MoonShine\Support\Enums\PageType;

/**
 * @template-covariant  I of PageContract

 * @mixin I
 */
interface PageContract extends
    HasCoreContract,
    HasComponentsContract,
    HasResourceContract,
    MenuFillerContract,
    HasAssetsContract,
    Renderable,
    HasUriKeyContract,
    HasStructureContract,
    StatefulContract
{
    public function getRouter(): RouterContract;

    public function getTitle(): string;

    public function getSubtitle(): string;

    public function getLayout(): LayoutContract;

    public function getRoute(array $params = []): string;

    public function loaded(): static;

    public function isCheckUrl(): bool;

    public function checkUrl(): static;

    public function getBreadcrumbs(): array;

    public function getPageType(): ?PageType;
}
