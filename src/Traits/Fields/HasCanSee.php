<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Traits\Fields;

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
        return is_callable($this->canSeeCallback)
            ? call_user_func($this->canSeeCallback, $data)
            : true;
    }
}
