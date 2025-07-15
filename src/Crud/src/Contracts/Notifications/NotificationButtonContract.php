<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Notifications;

use Illuminate\Contracts\Support\Arrayable;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;

/**
 * @mixin Arrayable<string, mixed>
 */
interface NotificationButtonContract
{
    public function getLink(): string;

    public function getLabel(): string;

    public function getAttributes(): ComponentAttributesBagContract;
}
