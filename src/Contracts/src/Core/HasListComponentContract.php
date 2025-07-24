<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Support\Enums\JsEvent;

interface HasListComponentContract
{
    public function getListComponent(bool $withoutFragment = false): ?ComponentContract;

    /**
     * @param  array<string, mixed>  $params
     */
    public function getListEventName(?string $name = null, array $params = []): string;

    public function isListComponentRequest(): bool;

    public function getListEventType(): JsEvent;

    public function getListComponentName(): string;
}
