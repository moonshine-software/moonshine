<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use MoonShine\Support\DTOs\AsyncCallback;

interface HasAsyncContract
{
    public function isAsync(): bool;

    public function disableAsync(): static;

    /**
     * @param  string|string[]|null  $events
     */
    public function async(
        Closure|string|null $url = null,
        string|array|null $events = null,
        ?AsyncCallback $callback = null,
    ): static;

    /**
     * @param  Closure(static $ctx): static  $callback
     */
    public function whenAsync(Closure $callback): static;

    public function getAsyncUrl(): ?string;

    public function getAsyncEvents(): string|null;

    public function getAsyncCallback(): ?AsyncCallback;
}
