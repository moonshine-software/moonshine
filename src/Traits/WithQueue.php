<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait WithQueue
{
    protected bool $queue = false;

    protected function isQueue(): bool
    {
        return $this->queue;
    }

    public function queue(): self
    {
        $this->queue = true;

        return $this;
    }
}
