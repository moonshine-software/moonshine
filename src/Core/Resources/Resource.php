<?php

declare(strict_types=1);

namespace MoonShine\Core\Resources;

use MoonShine\Core\Contracts\MenuFiller;
use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Core\Handlers\Handler;
use MoonShine\Core\Handlers\Handlers;
use MoonShine\Core\MoonShineRouter;
use MoonShine\Core\Pages\Pages;
use MoonShine\Support\Traits\WithAssets;
use MoonShine\Support\Traits\WithUriKey;

abstract class Resource implements ResourceContract, MenuFiller
{
    use WithUriKey;
    use WithAssets;

    protected string $title = '';

    protected ?Pages $pages = null;

    protected bool $booted = false;

    protected bool $loaded = false;

    public function __construct()
    {
        $this->booted();
    }

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
        //
    }

    public function booted(): static
    {
        if ($this->booted) {
            return $this;
        }

        $this->bootTraits();
        $this->onBoot();

        $this->booted = true;

        return $this;
    }

    protected function onLoad(): void
    {
        //
    }

    public function loaded(): static
    {
        if ($this->loaded) {
            return $this;
        }

        $this->onLoad();

        $this->loaded = true;

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
        return moonshineRouter()->extractResourceUri()
            === $this->uriKey();
    }
}
