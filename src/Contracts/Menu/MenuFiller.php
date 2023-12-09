<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Menu;

use Illuminate\Http\Request;

interface MenuFiller
{
    public function url(): string;

    public function isActive(): bool;

    public function canSee(Request $request): bool;
}
