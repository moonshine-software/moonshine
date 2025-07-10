<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Closure;

trait HasCanSee
{
    protected ?Closure $canSeeCallback = null;

    public function hasCanSeeCallback(): bool
    {
        return $this->canSeeCallback !== null;
    }

    public function canSee(Closure $callback): static
    {
        $this->canSeeCallback = $callback;

        return $this;
    }

    public function isSee(): bool
    {
        if (! $this->hasCanSeeCallback()) {
            return true;
        }

        $params = [
            ...$this->isSeeParams(),
            $this,
        ];

        return (bool) \call_user_func(
            $this->canSeeCallback,
            ...$params,
        );
    }

    /**
     * @return array<array-key, mixed>
     */
    protected function isSeeParams(): array
    {
        return [];
    }
}
