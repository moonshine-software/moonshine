<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Resources;

use MoonShine\MoonShineRouter;
use MoonShine\Pages\Pages;

interface ResourceContract
{
    public function getPages(): Pages;

    public function uriKey(): string;

    public function url(): string;

    public function router(): MoonShineRouter;

    public function title(): string;

    public function boot(): static;
}
