<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\HasCanSeeContract;
use MoonShine\Contracts\Core\RenderableContract;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Core\Core;
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

    abstract public function isActive(): bool;

    // todo DI
    // todo inject resolver
    protected CoreContract $core;

    public function __construct()
    {
        $this->attributes = new MoonShineComponentAttributeBag();

        // todo DI or inject from MenuManager and remove Core package and ??? move WithViewRenderer to Support
        $this->core = Core::getInstance();
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
            'previewLabel' => str($this->getLabel())->limit(2),
            'icon' => $this->getIcon(6),
            'isActive' => $this->isActive(),
            'top' => $this->isTopMode(),
        ];
    }
}
