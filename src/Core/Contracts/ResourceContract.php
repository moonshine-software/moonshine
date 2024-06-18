<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts;

use MoonShine\Core\MoonShineRouter;
use MoonShine\Core\Pages\Pages;

interface ResourceContract
{
    public function getPages(): Pages;

    public function getUriKey(): string;

    public function getUrl(): string;

    public function getRouter(): MoonShineRouter;

    public function getTitle(): string;

    public function booted(): static;

    public function loaded(): static;
}
