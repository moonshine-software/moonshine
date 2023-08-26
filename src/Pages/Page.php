<?php

declare(strict_types=1);

namespace MoonShine\Pages;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Contracts\HasResourceContract;
use MoonShine\Contracts\Menu\MenuFiller;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\MoonShineRouter;
use MoonShine\Traits\HasResource;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithUriKey;
use MoonShine\Traits\WithView;

abstract class Page implements MoonShineRenderable, HasResourceContract, MenuFiller
{
    use Makeable;
    use HasResource;
    use WithUriKey;
    use WithView;

    protected string $title = '';

    protected string $subtitle = '';

    protected string $layout = 'moonshine::layouts.app';

    public function __construct(?string $title = null)
    {
        if (! is_null($title)) {
            $this->setTitle($title);
        }

        $this->customView('moonshine::page');
    }

    abstract public function components(): array;

    public function breadcrumbs(): array
    {
        if (! $this->hasResource()) {
            return [];
        }

        return [
            to_page($this->getResource()) => $this->getResource()->title(),
        ];
    }

    public function getComponents(): PageComponents
    {
        return PageComponents::make($this->components());
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function subtitle(): string
    {
        return $this->subtitle;
    }

    public function layout(): string
    {
        return $this->layout;
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

    public function render(): View|Closure|string
    {
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
        ]);
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}
