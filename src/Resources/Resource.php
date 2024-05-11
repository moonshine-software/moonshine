<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Handlers\Handler;
use MoonShine\Handlers\Handlers;
use MoonShine\MenuManager\MenuFiller;
use MoonShine\MoonShineRouter;
use MoonShine\Pages\Pages;
use MoonShine\Traits\WithAssets;
use MoonShine\Traits\WithUriKey;

abstract class Resource implements ResourceContract, MenuFiller
{
    use WithUriKey;
    use WithAssets;

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

    protected function handlers(): array
    {
        return [];
    }

    public function getHandlers(): Handlers
    {
        return Handlers::make($this->handlers())
            ->each(fn (Handler $handler): Handler => $handler->setResource($this));
    }

    public function title(): string
    {
        return $this->title;
    }

    public function router(): MoonShineRouter
    {
        return moonshineRouter()->withResource($this);
    }

    public function url(): string
    {
        return $this->router()
            ->withPage($this->getPages()->first())
            ->to('resource.page')
        ;
    }

    public function isActive(): bool
    {
        return moonshineRequest()->getResourceUri()
            === $this->uriKey();
    }
}
