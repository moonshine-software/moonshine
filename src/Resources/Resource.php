<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use MoonShine\Contracts\Menu\MenuFiller;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Handlers\Handlers;
use MoonShine\Pages\Pages;
use MoonShine\Traits\WithUriKey;

abstract class Resource implements ResourceContract, MenuFiller
{
    use WithUriKey;

    protected string $title = '';

    protected ?Pages $pages = null;

    protected bool $booted = false;

    abstract protected function pages(): array;

    public function getPages(): Pages
    {
        if (! is_null($this->pages)) {
            return $this->pages;
        }

        $this->pages = Pages::make($this->pages())
            ->setResource($this);

        return $this->pages;
    }

    protected function onBoot(): void
    {
    }

    public function boot(): static
    {
        if ($this->booted) {
            return $this;
        }

        $this->bootTraits();
        $this->onBoot();

        $this->booted = true;

        return $this;
    }

    protected function bootTraits(): void
    {
        $class = static::class;

        $booted = [];

        foreach (class_uses_recursive($class) as $trait) {
            $method = 'boot' . class_basename($trait);

            if (method_exists($class, $method) && ! in_array($method, $booted, true)) {
                $this->{$method}();

                $booted[] = $method;
            }
        }
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

    public function title(): string
    {
        return $this->title;
    }

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

    public function canSee(Request $request): bool
    {
        return true;
    }

}
