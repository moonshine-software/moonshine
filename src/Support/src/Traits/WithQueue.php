<?php

declare(strict_types=1);

namespace MoonShine\Support\Traits;

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
