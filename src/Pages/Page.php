<?php

declare(strict_types=1);

namespace MoonShine\Pages;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Contracts\HasResourceContract;
use MoonShine\Contracts\Menu\MenuFiller;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Enums\PageType;
use MoonShine\MoonShineRouter;
use MoonShine\Traits\HasResource;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithUriKey;
use MoonShine\Traits\WithView;

/**
 * @method static static make(?string $title = null, ?string $alias = null, ?ResourceContract $resource = null)
 */
abstract class Page implements MoonShineRenderable, HasResourceContract, MenuFiller
{
    use Makeable;
    use HasResource;
    use WithUriKey;
    use WithView;

    protected string $title = '';

    protected string $subtitle = '';

    protected string $layout = 'moonshine::layouts.app';

    protected ?string $contentView = null;


    protected ?PageType $pageType = null;

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

        $this->customView('moonshine::page');
    }

    abstract public function components(): array;

    public function beforeRender(): void
    {
        //
    }

    public function fields(): array
    {
        return [];
    }

    protected function topLayer(): array
    {
        return [];
    }

    protected function mainLayer(): array
    {
        return [];
    }

    protected function bottomLayer(): array
    {
        return [];
    }

    public function pageType(): ?PageType
    {
        return $this->pageType;
    }

    public function breadcrumbs(): array
    {
        if (! $this->hasResource()) {
            return [];
        }

        return [
            to_page(resource: $this->getResource()) => $this->getResource()->title(),
        ];
    }

    public function getComponents(): PageComponents
    {
        return PageComponents::make($this->components());
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

    public function setLayout(string $layout): static
    {
        $this->layout = $layout;

        return $this;
    }

    public function layout(): string
    {
        return $this->layout;
    }

    public function setContentView(string $contentView): static
    {
        $this->contentView = $contentView;

        return $this;
    }

    public function contentView(): ?string
    {
        return $this->contentView;
    }

    public function route(array $params = []): string
    {
        return MoonShineRouter::to(
            'resource.page',
            [
                'resourceUri' => $this->getResource()->uriKey(),
                'pageUri' => $this->uriKey(),
            ] + $params
        );
    }

    public function url(): string
    {
        return MoonShineRouter::to('page', [
            'pageUri' => $this->uriKey(),
        ]);
    }

    public function isActive(): bool
    {
        return moonshineRequest()->getPageUri()
            === $this->uriKey();
    }

    protected function viewData(): array
    {
        return [];
    }

    public function render(): View|Closure|string
    {
        $data = $this->viewData();

        request()
            ?->route()
            ?->setParameter('pageUri', $this->uriKey());

        return view($this->getView(), [
            'layout' => $this->layout(),
            'title' => $this->title(),
            'subtitle' => $this->subtitle(),
            'resource' => $this->hasResource()
                ? $this->getResource()
                : null,
            'breadcrumbs' => $this->breadcrumbs(),
            'components' => $this->getComponents(),
            'contentView' => $this->contentView(),
        ] + $data)
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
