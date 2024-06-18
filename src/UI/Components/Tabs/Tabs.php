<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Tabs;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\UI\Collections\MoonShineRenderElements;
use MoonShine\UI\Components\AbstractWithComponents;
use MoonShine\UI\Components\Components;
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
     * @throws Throwable
     */
    public function getTabsLabels(): Collection
    {
        return $this->getTabs()->mapWithKeys(fn (Tab $tab): array => [
            $tab->getId() => $tab->getIcon(6, 'secondary')
                . PHP_EOL . $tab->getLabel(),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function getContents(): Collection
    {
        return $this->getTabs()->mapWithKeys(fn (Tab $tab): array => [
            $tab->getId() => Components::make(
                $tab->getComponents()
            ),
        ]);
    }

    /**
     * @return MoonShineRenderElements<int, Tab>
     * @throws Throwable
     */
    public function getTabs(): MoonShineRenderElements
    {
        return tap(
            $this->getComponents(),
            static function (MoonShineRenderElements $tabs): void {
                throw_if(
                    $tabs->every(fn ($tab): bool => ! $tab instanceof Tab),
                    MoonShineComponentException::onlyTabAllowed()
                );
            }
        );
    }

    /**
     * @throws Throwable
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        return [
            'tabs' => $this->getTabsLabels()->toArray(),
            'contents' => $this->getContents()->toArray(),
            'active' => $this->getActive(),
            'justifyAlign' => $this->getJustifyAlign(),
            'isVertical' => $this->isVertical(),
        ];
    }
}
