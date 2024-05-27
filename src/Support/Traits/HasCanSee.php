<?php

declare(strict_types=1);

namespace MoonShine\Support\Traits;

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
        if(is_null($this->canSeeCallback)) {
            return true;
        }

        return value($this->canSeeCallback, $data);
    }

    protected function isSeeParams(): array
    {
        return [
            $this,
        ];
    }
}
