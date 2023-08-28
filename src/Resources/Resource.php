<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use Illuminate\Support\Facades\Route;
use MoonShine\Contracts\Menu\MenuFiller;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Handlers\Handlers;
use MoonShine\Pages\Pages;
use MoonShine\Traits\WithUriKey;

abstract class Resource implements ResourceContract, MenuFiller
{
    use WithUriKey;

    abstract protected function pages(): array;

    public function getPages(): Pages
    {
        return Pages::make($this->pages())
            ->setResource($this);
    }

    public function routes(): void
    {
        Route::prefix('resource/{resourceUri}')->group(function (): void {
            $this->resolveRoutes();
        });
    }

    protected function resolveRoutes(): void
    {
        //
    }

    protected function handlers(): array
    {
        return [];
    }

    public function getHandlers(): Handlers
    {
        return Handlers::make($this->handlers());
    }

    // MenuFiller methods
    public function url(): string
    {
        return $this
            ->getPages()
            ->first()
            ->route();
    }

    public function isActive(): bool
    {
        return moonshineRequest()->getResourceUri()
            === $this->uriKey();
    }
}
