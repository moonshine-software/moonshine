<?php

declare(strict_types=1);

namespace MoonShine\Traits;

trait HasAsync
{
    protected ?string $asyncUrl = null;

    protected string|array|null $asyncEvents = null;

    public function isAsync(): bool
    {
        return ! is_null($this->asyncUrl);
    }

    protected function prepareAsyncUrl(?string $asyncUrl = null): ?string
    {
        return $asyncUrl;
    }

    public function async(?string $asyncUrl = null, string|array|null $asyncEvents = null): static
    {
        $this->asyncUrl = $this->prepareAsyncUrl($asyncUrl);
        $this->asyncEvents = $asyncEvents;

        return $this;
    }

    public function asyncUrl(): ?string
    {
        return $this->asyncUrl;
    }

    public function asyncEvents(): string|array|null
    {
        return is_array($this->asyncEvents)
            ? collect($this->asyncEvents)
                ->map(fn ($value): string => (string) str($value)->squish())
                ->filter()
                ->implode(',')
            : $this->asyncEvents;
    }
}
