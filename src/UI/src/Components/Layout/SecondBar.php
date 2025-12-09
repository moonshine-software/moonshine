<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Layout;

use Closure;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\UI\Components\AbstractWithComponents;

/**
 * @method static static make(iterable $components = [])
 */
class SecondBar extends AbstractWithComponents
{
    protected string $view = 'moonshine::components.layout.second-bar';

    protected array $translates = [
        'collapse_menu' => 'moonshine::ui.collapse_menu',
    ];

    public ComponentAttributesBagContract $collapseAttributes;

    public function __construct(
        iterable $components = [],
        public bool $collapsed = false
    ) {
        parent::__construct($components);

        $this->collapseAttributes = new MoonShineComponentAttributeBag();
    }

    public function collapsed(Closure|bool $condition = true): static
    {
        $this->collapsed = value($condition, $this) ?? true;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function collapseAttributes(array $attributes): static
    {
        $this->collapseAttributes = $this->collapseAttributes->merge($attributes);

        return $this;
    }

    protected function viewData(): array
    {
        /** @var ?Menu $menu */
        $menu = $this->getComponents()->first(fn (ComponentContract $component): bool => $component instanceof Menu);

        return [
            'hasMenu' => $menu?->items?->isNotEmpty() ?? false,
        ];
    }
}
