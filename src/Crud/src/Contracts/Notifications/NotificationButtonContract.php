<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Notifications;

use Illuminate\Contracts\Support\Arrayable;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;

/**
 * @extends Arrayable<string, mixed>
 */
interface NotificationButtonContract extends Arrayable
{
    /** @return array<string, mixed> */
    public function toArray(): array;

    public function getLink(): string;

    public function getLabel(): string;

    public function getAttributes(): ComponentAttributesBagContract;
}
