<?php

declare(strict_types=1);

namespace MoonShine\Pages;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Contracts\HasResourceContract;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\MoonShineRouter;
use MoonShine\Traits\HasResource;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithUriKey;
use MoonShine\Traits\WithView;

abstract class Page implements MoonShineRenderable, HasResourceContract
{
    use Makeable;
    use HasResource;
    use WithUriKey;
    use WithView;

    protected string $subtitle = '';

    public function __construct(
        protected string $title
    ) {
        $this->customView('moonshine::page');
    }

    abstract public function components(): array;

    public function breadcrumbs(): array
    {
        return [
            $this->route() => $this->title(),
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

    public function route(array $params = []): string
    {
        return MoonShineRouter::to('page', [
            'resourceUri' => $this->getResource()->uriKey(),
            'pageUri' => $this->uriKey(),
        ] + $params);
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
