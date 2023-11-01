<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use Closure;

/**
 * @method static static make(Closure|string $label)
 */
class Divider extends Decoration
{
    protected string $view = 'moonshine::decorations.divider';

    protected bool $isCentered = false;

    public function centered(): self
    {
        $this->isCentered = true;

        return $this;
    }

    public function isCentered(): bool
    {
        return $this->isCentered;
    }
}
