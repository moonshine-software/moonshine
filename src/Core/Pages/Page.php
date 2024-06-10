<?php

declare(strict_types=1);

namespace MoonShine\Core\Pages;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use MoonShine\Core\Contracts\HasResourceContract;
use MoonShine\Core\Contracts\MenuFiller;
use MoonShine\Core\Contracts\PageContract;
use MoonShine\Core\Contracts\PageView;
use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Core\MoonShineRouter;
use MoonShine\Support\Enums\Layer;
use MoonShine\Support\Enums\PageType;
use MoonShine\Support\Traits\HasResource;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithAssets;
use MoonShine\Support\Traits\WithUriKey;
use MoonShine\UI\Collections\ComponentsCollection;
use MoonShine\UI\Components\MoonShineComponent;
use MoonShine\UI\Contracts\Collections\FieldsCollection;
use MoonShine\UI\Contracts\Components\HasComponents;
use MoonShine\UI\Contracts\Fields\HasAssets;
use MoonShine\UI\Contracts\MoonShineRenderable;
use MoonShine\UI\MoonShineLayout;
use MoonShine\UI\Traits\WithViewRenderer;
use Stringable;

/**
 * @method static static make(?string $title = null, ?string $alias = null, ?ResourceContract $resource = null)
 * @template-covariant F of FieldsCollection
 */
abstract class Page implements
    PageContract,
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

    protected ?string $pageView = null;

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

        $this->resolveAssets();

        $this->booted();
    }

    protected function booted(): void
    {
        //
    }

    /**
     * @return list<MoonShineComponent>
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
     * @return MoonShineComponent
     */
    public function fields(): array
    {
        return [];
    }

    /**
     * @return F
     */
    public function getFields(): FieldsCollection
    {
        return fieldsCollection($this->fields());
    }

    /**
     * @return MoonShineComponent
     */
    protected function topLayer(): array
    {
        return [];
    }

    /**
     * @return MoonShineComponent
     */
    protected function mainLayer(): array
    {
        return [];
    }

    /**
     * @return MoonShineComponent
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
            $pageView = moonshine()->getContainer($this->pageView);

            return $pageView->components($this);
        }

        if (! is_null($this->components)) {
            return $this->components;
        }

        $this->components = ComponentsCollection::make($this->components());

        return $this->components;
    }

    /**
     * @return MoonShineComponent
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
     * @return MoonShineComponent
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

        return moonshine()->getContainer($this->layout);
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

        if ($this->hasResource()) {
            $router = $this->getResource()?->router();
        }

        return $router->withPage($this);
    }

    public function isActive(): bool
    {
        return moonshineRouter()->extractPageUri() === $this->uriKey();
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
        $withoutQuery = parse_url($this->url(), PHP_URL_PATH);

        if ($this->isCheckUrl() && trim($withoutQuery, '/') !== trim(moonshine()->getRequest()->getPath(), '/')) {
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
        return $view;
    }
}
