<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Traits\HasCanSee;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithComponentAttributes;
use MoonShine\Traits\WithIcon;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithView;

abstract class MenuElement implements MoonShineRenderable
{
    use Makeable;
    use WithComponentAttributes;
    use WithIcon;
    use HasCanSee;
    use WithLabel;
    use WithView;

    private bool $topMode = false;

    abstract public function isActive(): bool;

    public function viewData(): array
    {
        return [];
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


    public function render(): View
    {
        return view($this->getView(), [
            ...$this->viewData(),
            'attributes' => $this->attributes(),
            'label' => $this->label(),
            'icon' => $this->iconValue() ? $this->getIcon(6) : '',
            'isActive' => $this->isActive(),
            'top' => $this->isTopMode(),
        ]);
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}
