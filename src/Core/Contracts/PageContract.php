<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

use MoonShine\Core\MoonShineRouter;

interface PageContract
{
    public function uriKey(): string;

    public function url(): string;

    public function router(): MoonShineRouter;

    public function title(): string;
}
