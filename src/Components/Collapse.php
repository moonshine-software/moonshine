<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use MoonShine\Support\Condition;
use MoonShine\Traits\WithLabel;

/**
 * @method static static make(Closure|string $label, iterable $components = [])
 */
class Collapse extends AbstractWithComponents
{
    use WithLabel;

    protected string $view = 'moonshine::components.collapse';

    protected bool $open = false;

    protected bool $persist = true;

    public function __construct(
        Closure|string $label,
        iterable $components = []
    )
    {
        $this->setLabel($label);

        parent::__construct($components);
    }

    public function open(Closure|bool|null $condition = null): self
    {
        $this->open = Condition::boolean($condition, true);

        return $this;
    }

    public function isOpen(): bool
    {
        return $this->open;
    }

    public function persist(Closure|bool|null $condition = null): self
    {
        $this->persist = Condition::boolean($condition, true);

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
        ];
    }
}
