<?php

declare(strict_types=1);

namespace MoonShine\Support\Traits;

/**
 * @phpstan-ignore trait.unused
 */
trait WithQueue
{
    protected bool $queue = false;

    public function queue(): static
    {
        $this->queue = true;

        return $this;
    }

    protected function isQueue(): bool
    {
        return $this->queue;
    }
}
