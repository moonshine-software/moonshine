<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Closure;
use MoonShine\Support\Condition;

class Collapse extends Decoration
{
    protected string $view = 'moonshine::decorations.collapse';

    protected bool $open = false;

    protected bool $persist = true;

    /**
     * @deprecated will be removed in 3.0 (use method open())
     * @param bool $show
     * @return $this
     */
    public function show(Closure|bool|null $condition = null): self
    {
        return $this->open($condition);
    }

    /**
     * @deprecated will be removed in 3.0 (use method isOpen())
     */
    public function isShow(): bool
    {
        return $this->isOpen();
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
}
