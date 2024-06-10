<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

use MoonShine\Core\MoonShineRouter;
use MoonShine\Core\Pages\Pages;

interface ResourceContract
{
    public function getPages(): Pages;

    public function uriKey(): string;

    public function url(): string;

    public function router(): MoonShineRouter;

    public function title(): string;

    public function booted(): static;

    public function loaded(): static;
}
