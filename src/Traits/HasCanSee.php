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

    public function isSee(mixed $data)
    {
        return is_closure($this->canSeeCallback)
            ? call_user_func($this->canSeeCallback, $data)
            : true;
    }
}
