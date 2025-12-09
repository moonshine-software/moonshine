<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Contracts\MenuManager\MenuFillerContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\LayoutContract;
use MoonShine\Support\Enums\Layer;
use MoonShine\Support\Enums\PageType;

/**
 * @template TCore of CoreContract = CoreContract
 * @template TResource of ResourceContract = ResourceContract
 *
 * @extends HasCoreContract<TCore>
 * @extends HasResourceContract<TResource>
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
    public function getPageType(): ?PageType;

    public function getLayout(): LayoutContract;

    /**
     * @param  class-string<LayoutContract>  $layout
     */
    public function setLayout(string $layout): static;

    public function getRouter(): RouterContract;

    /**
     * @return list<MenuElementContract>
     */
    public function getMenu(): iterable;

    /**
     * @param  array<string, mixed>  $params
     *
     */
    public function getRoute(array $params = []): string;

    /**
     * @param  TResource|null  $resource
     *
     * @return $this
     */
    public function simulateRoute(?PageContract $page = null, ?ResourceContract $resource = null): static;

    public function getTitle(): string;

    public function title(string $title): static;

    public function getSubtitle(): string;

    public function subtitle(string $subtitle): static;

    /**
     * @return list<ComponentContract>
     */
    public function getLayers(): array;

    /**
     * @return list<ComponentContract>
     */
    public function getLayerComponents(Layer $layer): array;

    public function pushToLayer(Layer $layer, ComponentContract $component): static;

    /**
     * @return   array<string, string>
     *
     */
    public function getBreadcrumbs(): array;

    /**
     * @param  array<string, string>  $breadcrumbs
     *
     */
    public function breadcrumbs(array $breadcrumbs): static;

    public function isCheckUrl(): bool;

    public function checkUrl(): static;

    public function loaded(): static;
}
