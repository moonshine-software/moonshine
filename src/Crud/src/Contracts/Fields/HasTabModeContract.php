<?php

declare(strict_types=1);

namespace MoonShine\Crud\Contracts\Fields;

use Closure;

interface HasTabModeContract
{
    /**
     * @param (Closure(static $ctx): bool)|bool|null  $condition
     */
    public function tabMode(Closure|bool|null $condition = null): static;

    public function isTabMode(): bool;
}
