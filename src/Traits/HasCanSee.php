<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Closure;

trait HasCanSee
{
    protected ?Closure $canSeeCallback = null;

    public function canSee(Closure $callback): static
    {
        $this->canSeeCallback = $callback;

        return $this;
    }

    public function isSee(mixed $data): bool
    {
        return is_closure($this->canSeeCallback)
            ? value($this->canSeeCallback, $data)
            : true;
    }
}
