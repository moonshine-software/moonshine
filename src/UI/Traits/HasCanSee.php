<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Closure;

trait HasCanSee
{
    protected ?Closure $canSeeCallback = null;

    public function canSee(Closure $callback): static
    {
        $this->canSeeCallback = $callback;

        return $this;
    }

    public function isSee(mixed $data = null): bool
    {
        if(is_null($this->canSeeCallback)) {
            return true;
        }

        $params = [
            $data,
            ...$this->isSeeParams(),
        ];

        return (bool) value(
            $this->canSeeCallback,
            ...$params,
        );
    }

    protected function isSeeParams(): array
    {
        return [
            $this,
        ];
    }
}
