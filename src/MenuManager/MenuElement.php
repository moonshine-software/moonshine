<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use MoonShine\Contracts\Core\HasCanSeeContract;
use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Core\Traits\WithViewRenderer;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithComponentAttributes;
use MoonShine\UI\Traits\HasCanSee;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;

abstract class MenuElement implements MenuElementContract, RenderableContract, HasCanSeeContract
{
    use Makeable;
    use WithComponentAttributes;
    use WithIcon;
    use HasCanSee;
    use WithLabel;
    use WithViewRenderer;

    private bool $topMode = false;

    protected ?Closure $onIsActive = null;

    protected ?Closure $onFiller = null;

    protected ?Closure $onRender = null;

    abstract public function isActive(): bool;

    public function __construct()
    {
        $this->attributes = new MoonShineComponentAttributeBag();
    }

    public function topMode(?Closure $condition = null): static
    {
        $this->topMode = is_null($condition) || value($condition, $this);

        return $this;
    }

    public function onIsActive(Closure $onIsActive): static
    {
        $this->onIsActive = $onIsActive;

        return $this;
    }

    public function onFiller(Closure $onFiller): static
    {
        $this->onFiller = $onFiller;

        return $this;
    }

    public function onRender(Closure $onRender): static
    {
        $this->onRender = $onRender;

        return $this;
    }

    public function isTopMode(): bool
    {
        return $this->topMode;
    }

    protected function renderView(): Renderable|Closure|string
    {
        return value($this->onRender, $this->getView(), $this->toArray());
    }

    protected function systemViewData(): array
    {
        return [
            'type' => class_basename($this),
            'attributes' => $this->getAttributes(),
            'label' => $this->getLabel(),
            'previewLabel' => str($this->getLabel())->limit(2),
            'icon' => $this->getIcon(6),
            'isActive' => $this->isActive(),
            'top' => $this->isTopMode(),
        ];
    }
}
