<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentSlot;
use MoonShine\Contracts\UI\Collection\ComponentsContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Exceptions\ComponentException;
use Throwable;

class Tabs extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.tabs';

    protected string|int|null $active = null;

    protected string $justifyAlign = 'start';

    protected bool $vertical = false;

    /**
     * @param  iterable<array-key, ComponentContract>  $components
     * @param  array<string, string>  $items
     *
     * @throws Throwable
     */
    public function __construct(iterable $components = [], public array $items = [])
    {
        parent::__construct($components);

        if ($this->items !== []) {
            $tabs = [];

            foreach ($this->items as $label => $content) {
                $tabs[] = Tab::make($label, [
                    FlexibleRender::make($content),
                ]);
            }

            $this->setComponents($tabs);
        }
    }

    public function active(string|int $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function vertical(Closure|bool|null $condition = null): static
    {
        $this->vertical = value($condition, $this) ?? true;

        return $this;
    }

    public function isVertical(): bool
    {
        return $this->vertical;
    }

    public function justifyAlign(string $justifyAlign): static
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
    public function getTabs(): ComponentsContract
    {
        return tap(
            $this->getComponents(),
            static function (ComponentsContract $tabs): void {
                if ($tabs->every(static fn ($tab): bool => ! $tab instanceof Tab)) {
                    throw ComponentException::onlyTabAllowed();
                }
            }
        );
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        /** @var Collection<array-key, Tab> $tabs */
        $tabs = $this->getTabs();

        return [
            'tabs' => $tabs
                ->filter(fn (Tab $tab): bool => $tab->isSee())
                ->mapWithKeys(fn (Tab $tab): array => [$tab->getId() => $tab->toArray()])
                ->toArray(),
            'active' => $this->getActive(),
            'justifyAlign' => $this->getJustifyAlign(),
            'isVertical' => $this->isVertical(),
        ];
    }

    protected function resolveRender(): Renderable|Closure|string
    {
        return function (array|self $component): Renderable|Closure|string {
            if ($component instanceof self) {
                return $this->renderView();
            }

            $tabs = [];

            foreach ($component['items'] as $id => $label) {
                if ($component['__laravel_slots'][$id] ?? false) {
                    /** @var ComponentSlot $slot */
                    $slot = $component['__laravel_slots'][$id];
                    $attributes = $slot->attributes->jsonSerialize();

                    $tabs[$id] = Tab::make($label, [
                        FlexibleRender::make($slot->toHtml()),
                    ])
                        ->setId($id)
                        ->customAttributes($attributes)
                        ->toArray();
                }
            }

            $component['tabs'] = $tabs;

            return view(
                $this->getView(),
                $component
            );
        };
    }
}
