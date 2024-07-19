<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Tabs;

use Closure;
use MoonShine\Contracts\UI\RenderablesContract;
use MoonShine\UI\Components\AbstractWithComponents;
use MoonShine\UI\Exceptions\MoonShineComponentException;
use Throwable;

class Tabs extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.tabs';

    protected string|int|null $active = null;

    protected string $justifyAlign = 'start';

    protected bool $vertical = false;

    public function active(string|int $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function vertical(Closure|bool|null $condition = null): self
    {
        $this->vertical = value($condition, $this) ?? true;

        return $this;
    }

    public function isVertical(): bool
    {
        return $this->vertical;
    }

    public function justifyAlign(string $justifyAlign): self
    {
        $this->justifyAlign = $justifyAlign;

        return $this;
    }

    public function getJustifyAlign(): string
    {
        return $this->justifyAlign;
    }

    /**
     * @throws Throwable
     */
    public function getActive(): string|int|null
    {
        return $this->getTabs()->firstWhere('active', true)?->getId();
    }

    /**
     * @return RenderablesContract<int, Tab>
     * @throws Throwable
     */
    public function getTabs(): RenderablesContract
    {
        return tap(
            $this->getComponents(),
            static function (RenderablesContract $tabs): void {
                throw_if(
                    $tabs->every(static fn ($tab): bool => ! $tab instanceof Tab),
                    MoonShineComponentException::onlyTabAllowed()
                );
            }
        );
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'tabs' => $this->getTabs()
                ->mapWithKeys(fn(Tab $tab) => [$tab->getId() => $tab->toArray()])
                ->toArray(),
            'active' => $this->getActive(),
            'justifyAlign' => $this->getJustifyAlign(),
            'isVertical' => $this->isVertical(),
        ];
    }
}
