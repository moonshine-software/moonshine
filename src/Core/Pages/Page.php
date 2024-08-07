<?php

declare(strict_types=1);

namespace MoonShine\Core\Pages;

use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\Core\HasAssetsContract;
use MoonShine\Contracts\Core\HasComponentsContract;
use MoonShine\Contracts\Core\HasResourceContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Contracts\MenuManager\MenuFillerContract;
use MoonShine\Contracts\UI\LayoutContract;
use MoonShine\Core\Collections\Components;
use MoonShine\Core\Traits\HasResource;
use MoonShine\Core\Traits\WithAssets;
use MoonShine\Core\Traits\WithCore;
use MoonShine\Core\Traits\WithUriKey;
use MoonShine\Core\Traits\WithViewRenderer;
use MoonShine\Support\Enums\Layer;
use MoonShine\Support\Enums\PageType;
use Stringable;

/**
 * @template-covariant F of FieldsContract
 */
abstract class Page implements
    PageContract,
    Renderable,
    HasComponentsContract,
    HasResourceContract,
    MenuFillerContract,
    HasAssetsContract,
    Stringable
{
    use WithCore;
    use HasResource;
    use WithUriKey;
    use WithAssets;
    use WithViewRenderer;

    protected string $title = '';

    protected string $subtitle = '';

    /** @var ?class-string<LayoutContract> */
    protected ?string $layout = null;

    protected ?iterable $components = null;

    protected array $layersComponents = [];

    protected ?array $breadcrumbs = null;

    protected ?PageType $pageType = null;

    protected bool $checkUrl = false;

    protected bool $loaded = false;

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
     * @return list<RenderableContract>
     */
    abstract public function components(): array;

    public function flushState(): void
    {
        $this->resource = null;
        $this->parentResource = null;
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
     * @return list<RenderableContract>
     */
    public function fields(): array
    {
        return [];
    }

    /**
     * @return F
     */
    public function getFields(): FieldsContract
    {
        return $this->getCore()->getFieldsCollection($this->fields());
    }

    /**
     * @return list<RenderableContract>
     */
    protected function topLayer(): array
    {
        return [];
    }

    /**
     * @return list<RenderableContract>
     */
    protected function mainLayer(): array
    {
        return [];
    }

    /**
     * @return list<RenderableContract>
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
        if (! is_null($this->breadcrumbs)) {
            return $this->breadcrumbs;
        }

        if (! $this->hasResource()) {
            return [];
        }

        return [
            $this->getResource()?->getUrl() => $this->getResource()?->getTitle(),
        ];
    }

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
        if (! is_null($this->components)) {
            return $this->components;
        }

        $this->components = Components::make($this->components());

        return $this->components;
    }

    /**
     * @return list<RenderableContract>
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
     * @return list<RenderableContract>
     */
    public function getLayerComponents(Layer $layer): array
    {
        return array_merge(
            $this->{$layer->value}(),
            $this->layersComponents[$layer->value] ?? []
        );
    }

    public function pushToLayer(Layer $layer, RenderableContract $component): static
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
     * @return $this
     */
    public function setLayout(string $layout): static
    {
        $this->layout = $layout;

        return $this;
    }

    public function getLayout(): LayoutContract
    {
        if (is_null($this->layout)) {
            $this->setLayout(
                $this->getCore()->getConfig()->getLayout()
            );
        }

        return $this->getCore()->getContainer($this->layout, null, page: $this);
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

        if ($this->hasResource()) {
            $router = $this->getResource()?->getRouter();
        }

        return $router->withPage($this);
    }

    protected function resolveAssets(): void
    {
        $assets = $this->getAssets() ?? [];

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
     * @return array<string, mixed>
     */
    protected function systemViewData(): array
    {
        return [
            'layout' => $this->getLayout()->build(),
        ];
    }

    public function getView(): string
    {
        return 'moonshine::layouts.app';
    }
}
