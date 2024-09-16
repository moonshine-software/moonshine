<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Support\DTOs\AsyncCallback;

interface HasAsyncContract
{
    public function isAsync(): bool;

    public function disableAsync(): static;

    public function async(
        Closure|string|null $url = null,
        string|array|null $events = null,
        ?AsyncCallback $callback = null,
    ): static;

    public function getAsyncUrl(): ?string;

    public function getAsyncEvents(): string|null;

    public function getAsyncCallback(): ?AsyncCallback;
}
