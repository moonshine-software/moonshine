<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Traits\HasCanSee;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithComponentAttributes;
use MoonShine\Support\Traits\WithIcon;
use MoonShine\Support\Traits\WithLabel;
use MoonShine\UI\Contracts\Components\HasCanSeeContract;
use MoonShine\UI\Contracts\MoonShineRenderable;
use MoonShine\UI\Traits\WithViewRenderer;

abstract class MenuElement implements MoonShineRenderable, HasCanSeeContract
{
    use Makeable;
    use WithComponentAttributes;
    use WithIcon;
    use HasCanSee;
    use WithLabel;
    use WithViewRenderer;

    private bool $topMode = false;

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

    public function isTopMode(): bool
    {
        return $this->topMode;
    }

    protected function systemViewData(): array
    {
        return [
            'type' => class_basename($this),
            'attributes' => $this->getAttributes(),
            'label' => $this->getLabel(),
            'icon' => $this->getIcon(6),
            'isActive' => $this->isActive(),
            'top' => $this->isTopMode(),
        ];
    }
}
