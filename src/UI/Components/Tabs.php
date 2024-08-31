<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\ComponentSlot;
use MoonShine\Contracts\UI\RenderablesContract;
use MoonShine\UI\Components\Tabs\Tab;
use MoonShine\UI\Exceptions\MoonShineComponentException;
use Throwable;

class Tabs extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.tabs';

    protected string|int|null $active = null;

    protected string $justifyAlign = 'start';

    protected bool $vertical = false;

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
                ->filter(fn (Tab $tab): bool => $tab->isSee())
                ->mapWithKeys(fn (Tab $tab) => [$tab->getId() => $tab->toArray()])
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
