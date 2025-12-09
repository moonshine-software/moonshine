<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use MoonShine\Contracts\Core\HasViewRendererContract;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use MoonShine\Core\Traits\WithCore;
use MoonShine\Core\Traits\WithViewRenderer;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Traits\Makeable;
use MoonShine\Support\Traits\WithComponentAttributes;
use MoonShine\UI\Traits\HasCanSee;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;

abstract class MenuElement implements MenuElementContract, HasViewRendererContract
{
    use Makeable;
    use Macroable;
    use WithCore;
    use WithComponentAttributes;
    use WithIcon;
    use HasCanSee;
    use WithLabel;
    use WithViewRenderer;
    use Conditionable;

    private bool $topMode = false;

    private bool $onlyIcon = false;

    abstract public function isActive(): bool;

    public function __construct()
    {
        $this->attributes = new MoonShineComponentAttributeBag();
    }

    public function topMode(Closure|bool|null $condition = true): static
    {
        $this->topMode = \is_null($condition) || value($condition, $this);

        return $this;
    }

    public function isTopMode(): bool
    {
        return $this->topMode;
    }

    public function onlyIcon(Closure|bool|null $condition = true): static
    {
        $this->onlyIcon = \is_null($condition) || value($condition, $this);

        return $this;
    }

    public function isOnlyIcon(): bool
    {
        if ($this->getLabel() === '') {
            return true;
        }

        return $this->onlyIcon;
    }

    /**
     * @return array<string, mixed>
     */
    protected function systemViewData(): array
    {
        return [
            'type' => class_basename($this),
            'attributes' => $this->getAttributes(),
            'label' => $this->getLabel(),
            'previewLabel' => Str::of($this->getLabel())->limit(3),
            'icon' => $this->getIcon(),
            'isActive' => $this->isActive(),
            'onlyIcon' => $this->isOnlyIcon(),
            'top' => $this->isTopMode(),
        ];
    }
}
