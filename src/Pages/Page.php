<?php

declare(strict_types=1);

namespace MoonShine\Pages;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use MoonShine\Collections\ComponentsCollection;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Contracts\Components\HasComponents;
use MoonShine\Contracts\Fields\HasAssets;
use MoonShine\Contracts\HasResourceContract;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Contracts\PageView;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Enums\Layer;
use MoonShine\Enums\PageType;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\MenuManager\MenuFiller;
use MoonShine\MoonShineLayout;
use MoonShine\MoonShineRouter;
use MoonShine\Traits\HasResource;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithAssets;
use MoonShine\Traits\WithUriKey;
use MoonShine\Traits\WithViewRenderer;
use Stringable;
use Throwable;

/**
 * @method static static make(?string $title = null, ?string $alias = null, ?ResourceContract $resource = null)
 */
abstract class Page implements
    Renderable,
    HasComponents,
    HasResourceContract,
    MenuFiller,
    HasAssets,
    Stringable
{
    use Makeable;
    use HasResource;
    use WithUriKey;
    use WithAssets;
    use WithViewRenderer;

    protected string $title = '';

    protected string $subtitle = '';

    /** @var ?class-string<MoonShineLayout> */
    protected ?string $layout = null;

    protected ?PageView $pageView = null;

    protected ?ComponentsCollection $components = null;

    protected array $layersComponents = [];

    protected ?array $breadcrumbs = null;

    protected ?PageType $pageType = null;

    protected bool $checkUrl = false;

    public function __construct(
        ?string $title = null,
        ?string $alias = null,
        ?ResourceContract $resource = null,
    ) {
        if (! is_null($title)) {
            $this->setTitle($title);
        }

        if (! is_null($alias)) {
            $this->alias($alias);
        }

        if (! is_null($resource)) {
            $this->setResource($resource);
        }

        $this->booted();
    }

    protected function booted(): void
    {
        //
    }

    /**
     * @return list<MoonShineComponent|Field>
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
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [];
    }

    /**
     * @return Collection<int, Field>
     * @throws Throwable
     */
    public function getFields(): Fields
    {
        return Fields::make($this->fields());
    }

    /**
     * @return list<MoonShineComponent>
     */
    protected function topLayer(): array
    {
        return [];
    }

    /**
     * @return list<MoonShineComponent>
     */
    protected function mainLayer(): array
    {
        return [];
    }

    /**
     * @return list<MoonShineComponent>
     */
    protected function bottomLayer(): array
    {
        return [];
    }

    public function pageType(): ?PageType
    {
        return $this->pageType;
    }

    /**
     * @return array<string, string>
     */
    public function breadcrumbs(): array
    {
        if (! is_null($this->breadcrumbs)) {
            return $this->breadcrumbs;
        }

        if (! $this->hasResource()) {
            return [];
        }

        return [
            $this->getResource()?->url() => $this->getResource()?->title(),
        ];
    }

    public function setBreadcrumbs(array $breadcrumbs): static
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
        if (! $components instanceof ComponentsCollection) {
            $components = ComponentsCollection::make($components);
        }

        $this->components = $components;

        return $this;
    }

    public function getComponents(): ComponentsCollection
    {
        if (! is_null($this->pageView)) {
            /** @var PageView $pageView */
            $pageView = app($this->pageView);

            return $pageView->components($this);
        }

        if (! is_null($this->components)) {
            return $this->components;
        }

        $this->components = ComponentsCollection::make($this->components());

        return $this->components;
    }

    /**
     * @return list<MoonShineComponent>
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
     * @return list<MoonShineComponent>
     */
    public function getLayerComponents(Layer $layer): array
    {
        return array_merge(
            $this->{$layer->value}(),
            $this->layersComponents[$layer->value] ?? []
        );
    }

    public function pushToLayer(Layer $layer, MoonShineRenderable $component): static
    {
        $this->layersComponents[$layer->value][] = $component;

        return $this;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function setSubTitle(string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function subtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * @param  class-string<MoonShineLayout>  $layout
     * @return $this
     */
    public function setLayout(string $layout): static
    {
        $this->layout = $layout;

        return $this;
    }

    public function layout(): MoonShineLayout
    {
        if (is_null($this->layout)) {
            $this->setLayout(
                moonshineConfig()->getLayout()
            );
        }

        return app($this->layout);
    }

    public function route(array $params = []): string
    {
        return $this->router()->to(
            $this->hasResource() ? 'resource.page' : 'page',
            $params
        );
    }

    public function url(): string
    {
        return $this->route();
    }

    public function router(): MoonShineRouter
    {
        $router = moonshineRouter();

        if($this->hasResource()) {
            $router = $this->getResource()?->router();
        }

        return $router->withPage($this);
    }

    public function isActive(): bool
    {
        return moonshineRequest()->getPageUri()
            === $this->uriKey();
    }

    /**
     * @return array<string, mixed>
     */
    protected function systemViewData(): array
    {
        return [
            'layout' => $this->layout()->build($this),
        ];
    }

    public function getView(): string
    {
        return 'moonshine::layouts.app';
    }

    protected function prepareBeforeRender(): void
    {
        request()
            ?->route()
            ?->setParameter('pageUri', $this->uriKey());

        $withoutQuery = parse_url($this->url(), PHP_URL_PATH);

        if ($this->isCheckUrl() && trim($withoutQuery, '/') !== trim((string) request()?->path(), '/')) {
            oops404();
        }
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
            moonshineAssets()->add($assets);
        }
    }

    protected function prepareRender(View|Closure|string $view): View|Closure|string
    {
        return $view->fragmentIf(
            moonshineRequest()->isFragmentLoad(),
            moonshineRequest()->getFragmentLoad()
        );
    }
}
