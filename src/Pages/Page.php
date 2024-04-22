<?php

declare(strict_types=1);

namespace MoonShine\Pages;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use MoonShine\Components\MoonShineComponent;
use MoonShine\Contracts\HasResourceContract;
use MoonShine\MenuManager\MenuFiller;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Contracts\PageView;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Enums\Layer;
use MoonShine\Enums\PageType;
use MoonShine\Layouts\AppLayout;
use MoonShine\MoonShineLayout;
use MoonShine\Fields\Field;
use MoonShine\Traits\HasResource;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithAssets;
use MoonShine\Traits\WithUriKey;
use Stringable;
use Throwable;

/**
 * @method static static make(?string $title = null, ?string $alias = null, ?ResourceContract $resource = null)
 */
abstract class Page implements Renderable, HasResourceContract, MenuFiller, Stringable
{
    use Makeable;
    use HasResource;
    use WithUriKey;
    use WithAssets;

    protected string $title = '';

    protected string $subtitle = '';

    /** @var ?class-string<MoonShineLayout> */
    protected ?string $layout = null;

    protected ?PageView $pageView = null;

    protected ?string $contentView = null;

    protected ?PageComponents $components = null;

    protected array $layersComponents = [];

    protected Closure|array $viewData = [];

    protected ?array $breadcrumbs = null;

    protected ?PageType $pageType = null;

    protected bool $checkUrl = false;

    public function __construct(?string $title = null, ?string $alias = null, ?ResourceContract $resource = null)
    {
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

    public function beforeRender(): void
    {
        request()
            ?->route()
            ?->setParameter('pageUri', $this->uriKey());

        $withoutQuery = parse_url($this->url(), PHP_URL_PATH);

        if ($this->isCheckUrl() && trim($withoutQuery, '/') !== trim((string) request()?->path(), '/')) {
            oops404();
        }

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

    /**
     * @return list<MoonShineComponent|Field>
     */
    public function fields(): array
    {
        return [];
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

    public function getComponents(): PageComponents
    {
        if (! is_null($this->pageView)) {
            /** @var PageView $pageView */
            $pageView = app($this->pageView);

            return $pageView->components($this);
        }

        if (! is_null($this->components)) {
            return $this->components;
        }

        $this->components = PageComponents::make($this->components());

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
                config('moonshine.layout', AppLayout::class)
            );
        }

        return app($this->layout);
    }

    // TODO unused
    public function setContentView(string $contentView, Closure|array $data = []): static
    {
        $this->contentView = $contentView;
        $this->viewData = $data;

        return $this;
    }

    // TODO unused
    public function contentView(): ?string
    {
        return $this->contentView;
    }

    public function route(array $params = []): string
    {
        return moonshineRouter()->to(
            $this->hasResource() ? 'resource.page' : 'page',
            [
                'resourceUri' => $this->getResource()?->uriKey(),
                'pageUri' => $this->uriKey(),
            ] + $params
        );
    }

    public function url(): string
    {
        return $this->route();
    }

    public function asyncMethodUrl(
        string $method,
        ?string $message = null,
        array $params = [],
        ?ResourceContract $resource = null,
    ): string {
        return moonshineRouter()->asyncMethod(
            $method,
            $message,
            $params,
            page: $this,
            resource: $resource
        );
    }

    /**
     * @throws Throwable
     */
    public function fragmentLoadUrl(
        string $fragment,
        array $params = []
    ): string {
        return moonshineRouter()->to_page(
            $this,
            params: array_filter($params),
            fragment: $fragment
        );
    }

    public function isActive(): bool
    {
        return moonshineRequest()->getPageUri()
            === $this->uriKey();
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            ...value($this->viewData),
        ];
    }

    public function render(): View|Closure|string
    {
        $this->beforeRender();

        return view('moonshine::layouts.app', [
            'layout' => $this->layout()->build($this),
        ])
            ->fragmentIf(
                moonshineRequest()->isFragmentLoad(),
                moonshineRequest()->getFragmentLoad()
            );
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}
