<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait HasAsync
{
    protected ?string $asyncUrl = null;

    protected ?string $asyncEvents = null;

    public function isAsync(): bool
    {
        return ! is_null($this->asyncUrl);
    }

    public function async(?string $asyncUrl = null, ?string $asyncEvents = null): static
    {
        $this->asyncUrl = $asyncUrl;
        $this->asyncEvents = $asyncEvents;

        return $this;
    }

    public function asyncUrl(): ?string
    {
        return $this->asyncUrl;
    }

    public function asyncEvents(): ?string
    {
        return $this->asyncEvents;
    }
}
