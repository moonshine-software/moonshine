<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use MoonShine\Exceptions\DecorationException;
use MoonShine\Traits\WithIcon;

class Tab extends Decoration
{
    use WithIcon;

    public bool $active = false;

    /**
     * @throws DecorationException
     */
    public function getView(): string
    {
        throw new DecorationException(
            'You need to use ' . Tabs::class . ' class'
        );
    }

    /**
     * @return $this
     */
    public function active(): static
    {
        $this->active = true;

        return $this;
    }
}
