<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait HasAsync
{
    protected ?string $asyncUrl = null;

    public function isAsync(): bool
    {
        return ! is_null($this->asyncUrl);
    }

    public function async(string $asyncUrl): self
    {
        $this->asyncUrl = $asyncUrl;

        return $this;
    }

    public function asyncUrl(): ?string
    {
        return $this->asyncUrl;
    }
}
