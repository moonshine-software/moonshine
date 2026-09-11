<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\View\ComponentSlot;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\HasIconContract;
use MoonShine\Contracts\UI\HasLabelContract;
use MoonShine\UI\Traits\WithIcon;
use MoonShine\UI\Traits\WithLabel;

/**
 * @method static static make(Closure|string $label = '', iterable<array-key, ComponentContract> $components = [])
 */
class Collapse extends AbstractWithComponents implements HasIconContract, HasLabelContract
{
    use WithLabel;
    use WithIcon;

    protected string $view = 'moonshine::components.collapse';

    public function __construct(
        Closure|string $label = '',
        iterable $components = [],
        public bool $open = false,
        public bool $persist = true,
    ) {
        $this->setLabel($label);

        parent::__construct($components);
    }

    /** @param (Closure(static): (bool|null))|bool|null $condition */
    public function open(Closure|bool|null $condition = null): static
    {
        $this->open = value($condition, $this) ?? true;

        return $this;
    }

    public function isOpen(): bool
    {
        return $this->open;
    }

    /** @param (Closure(static): (bool|null))|bool|null $condition */
    public function persist(Closure|bool|null $condition = null): static
    {
        $this->persist = value($condition, $this) ?? true;

        return $this;
    }

    public function isPersist(): bool
    {
        return $this->persist;
    }

    protected function viewData(): array
    {
        return [
            'persist' => $this->isPersist(),
            'open' => $this->isOpen(),
            'title' => $this->getLabel(),
            'icon' => new ComponentSlot(
                $this->getIcon(5)
            ),
        ];
    }
}
