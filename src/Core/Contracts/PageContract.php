<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

use MoonShine\Core\MoonShineRouter;

interface PageContract
{
    public function getUriKey(): string;

    public function getUrl(): string;

    public function getRouter(): MoonShineRouter;

    public function getTitle(): string;
}
