<?php

declare(strict_types=1);

namespace MoonShine\Core\Pages;

use Leeto\FastAttributes\Attributes;
use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\LayoutContract;
use MoonShine\Core\Attributes\Layout;
use MoonShine\Core\Collections\Components;
use MoonShine\Core\Traits\HasResource;
use MoonShine\Core\Traits\WithAssets;
use MoonShine\Core\Traits\WithCore;
use MoonShine\Core\Traits\WithUriKey;
use MoonShine\Core\Traits\WithViewRenderer;
use MoonShine\Support\Concerns\MenuFillerConcern;
use MoonShine\Support\Enums\Layer;
use MoonShine\Support\Enums\PageType;

/**
 * @template TCore of CoreContract = CoreContract
 * @template TResource of ResourceContract = ResourceContract
 *
 * @implements PageContract<TCore, TResource>
 */
abstract class Page implements PageContract
{
    /** @use WithCore<TCore> */
    use WithCore;
    /** @use HasResource<TResource> */
    use HasResource;
    use WithUriKey;
    use WithAssets;
    use WithViewRenderer;
    use MenuFillerConcern;

    protected string $title = '';

    protected string $subtitle = '';

    /** @var ?class-string<LayoutContract> */
    protected ?string $layout = null;

    /**
     * @var Components|list<ComponentContract>|null
     */
    protected ?iterable $components = null;

    /**
     * @var array<string, list<ComponentContract>>
     */
    protected array $layersComponents = [];

    /**
     * @var array<string, string>|null
     */
    protected ?array $breadcrumbs = null;

    protected ?PageType $pageType = null;

    protected bool $checkUrl = false;

    protected bool $loaded = false;

    /**
     * @param  TCore  $core
     */
    public function __construct(
        CoreContract $core,
    ) {
        $this->setCore($core);
        $this->booted();
    }

    protected function booted(): void
    {
        //
    }

    protected function onLoad(): void
    {
        $this->resolveAssets();
    }

    public function loaded(): static
    {
        if ($this->loaded) {
            return $this;
        }

        $this->onLoad();

        $this->loaded = true;

        return $this;
    }

    /**
     * @return list<ComponentContract>
     */
    abstract protected function components(): iterable;

    public function flushState(): void
    {
        $this->resource = null;
        $this->components = null;
        $this->breadcrumbs = null;
        $this->layersComponents = [];
    }

    public function isCheckUrl(): bool
    {
        return $this->checkUrl;
    }

    public function checkUrl(): static
    {
        $this->checkUrl = true;

        return $this;
    }

    /**
     * @return list<MenuElementContract>
     */
    protected function menu(): array
    {
        return [];
    }

    /**
     * @return list<MenuElementContract>
     */
    public function getMenu(): iterable
    {
        return $this->menu();
    }

    /**
     * @return list<ComponentContract>
     */
    protected function topLayer(): array
    {
        return [];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function mainLayer(): array
    {
        return [];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function bottomLayer(): array
    {
        return [];
    }

    public function getPageType(): ?PageType
    {
        return $this->pageType;
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        if (! \is_null($this->breadcrumbs)) {
            return $this->breadcrumbs;
        }

        if (! $this->hasResource()) {
            return [];
        }

        return [
            $this->getResource()?->getUrl() ?? '' => $this->getResource()?->getTitle() ?? '',
        ];
    }

    /**
     * @param  array<string, string>  $breadcrumbs
     *
     */
    public function breadcrumbs(array $breadcrumbs): static
    {
        $this->breadcrumbs = $breadcrumbs;

        return $this;
    }

    public function hasComponents(): bool
    {
        return $this->getComponents()->isNotEmpty();
    }

    public function setComponents(iterable $components): static
    {
        if (! $components instanceof Components) {
            $components = Components::make($components);
        }

        $this->components = $components;

        return $this;
    }

    public function getComponents(): Components
    {
        if (! \is_null($this->components)) {
            return $this->components instanceof Components
                ? $this->components
                : Components::make($this->components);
        }

        $this->components = Components::make($this->components());

        return $this->components;
    }

    /**
     * @return list<ComponentContract>
     */
    public function getLayers(): array
    {
        return [
            ...$this->getLayerComponents(Layer::TOP),
            ...$this->getLayerComponents(Layer::MAIN),
            ...$this->getLayerComponents(Layer::BOTTOM),
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    public function getLayerComponents(Layer $layer): array
    {
        return array_merge(
            $this->{$layer->value}(),
            $this->layersComponents[$layer->value] ?? []
        );
    }

    public function pushToLayer(Layer $layer, ComponentContract $component): static
    {
        $this->layersComponents[$layer->value][] = $component;

        return $this;
    }

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function subtitle(string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * @param  class-string<LayoutContract>  $layout
     */
    public function setLayout(string $layout): static
    {
        $this->layout = $layout;

        return $this;
    }

    public function getLayout(): LayoutContract
    {
        /** @var null|class-string<LayoutContract> $layout */
        $layout = $this->getCore()->getAttributes()->get(
            default: fn (): mixed => Attributes::for($this, Layout::class)->first('name'),
            target: static::class,
            attribute: Layout::class,
            column: [0 => 'name']
        );

        if (! \is_null($layout)) {
            $this->setLayout($layout);
        }

        if (\is_null($this->layout)) {
            $this->setLayout(
                $this->getCore()->getConfig()->getLayout()
            );
        }

        /** @var class-string<LayoutContract> $layout */
        $layout = $this->layout;
        $resolvedLayout = $this->getCore()->getContainer($layout);
        $resolvedLayout->setPage($this);

        return $this->modifyLayout($resolvedLayout);
    }

    protected function modifyLayout(LayoutContract $layout): LayoutContract
    {
        return $layout;
    }

    public function getRoute(array $params = []): string
    {
        return $this->getRouter()->to(
            $this->hasResource() ? 'resource.page' : 'page',
            $params
        );
    }

    public function getUrl(): string
    {
        return $this->getRoute();
    }

    public function getRouter(): RouterContract
    {
        $router = clone $this->getCore()->getRouter();
        $resource = $this->getResource();

        if (! \is_null($resource)) {
            $router = $resource->getRouter();
        }

        return $router->withPage($this);
    }

    protected function resolveAssets(): void
    {
        $assets = $this->getAssets();

        if ($this->hasResource()) {
            $assets = [
                ...$assets,
                ...$this->getResource()?->getAssets() ?? [],
            ];
        }

        if ($assets !== []) {
            $this->getCore()->getContainer(AssetManagerContract::class)->add($assets);
        }
    }

    public function isActive(): bool
    {
        return $this->getCore()->getRouter()->extractPageUri() === $this->getUriKey();
    }

    /**
     * @api
     *
     * @param null|TResource $resource
     */
    public function simulateRoute(?PageContract $page = null, ?ResourceContract $resource = null): static
    {
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function systemViewData(): array
    {
        return [
            'layout' => $this->getLayout()->build(),
        ];
    }

    protected function prepareBeforeRender(): void
    {
        if ($this->hasResource()) {
            $this->getResource()?->loaded();
        }

        $this->loaded();
    }

    public function getView(): string
    {
        return $this->customView ?? $this->view ?: 'moonshine::page';
    }
}
