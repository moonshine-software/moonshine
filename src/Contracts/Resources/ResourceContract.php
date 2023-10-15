<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Resources;

use MoonShine\Pages\Pages;

interface ResourceContract
{
    public function uriKey(): string;

    public function title(): string;

    public function getPages(): Pages;

    public function routes(): void;

    public function boot(): static;
}
