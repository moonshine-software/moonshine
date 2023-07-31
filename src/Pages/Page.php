<?php

declare(strict_types=1);

namespace MoonShine\Pages;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Exceptions\PageException;
use MoonShine\MoonShineRouter;
use MoonShine\Resources\Resource;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithUriKey;
use MoonShine\Traits\WithView;
use Throwable;

abstract class Page implements MoonShineRenderable
{
    use WithUriKey;
    use Makeable;
    use WithView;

    protected string $subtitle = '';

    protected ?ResourceContract $resource = null;

    public function __construct(
        protected string $title
    ) {
        $this->customView('moonshine::page');
    }

    abstract public function components(): array;

    public function breadcrumbs(): array
    {
        return [
            $this->route() => $this->title()
        ];
    }

    public function getComponents(): PageComponents
    {
        return PageComponents::make($this->components());
    }

    public function title(): string
    {
        return $this->title;
    }

    public function subtitle(string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function resource(Resource $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function getResource(): Resource
    {
        throw_if(is_null($this->resource), new PageException('Resource is required'));

        return $this->resource;
    }

    public function route(): string
    {
        return MoonShineRouter::to('page', [
            'resourceUri' => $this->getResource()->uriKey(),
            'pageUri' => $this->uriKey()
        ]);
    }

    public function render(): View|Closure|string
    {
        return view($this->getView(), [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'resource' => $this->getResource(),
            'breadcrumbs' => $this->breadcrumbs(),
            'components' => $this->getComponents(),
        ]);
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}