<?php

declare(strict_types=1);

namespace MoonShine\Core\Resources;

use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\MenuManager\MenuFillerContract;
use MoonShine\Core\Pages\Pages;
use MoonShine\Core\Traits\WithAssets;
use MoonShine\Core\Traits\WithUriKey;

abstract class Resource implements ResourceContract, MenuFillerContract
{
    use WithUriKey;
    use WithAssets;

    protected string $title = '';

    protected ?Pages $pages = null;

    protected bool $booted = false;

    protected bool $loaded = false;

    public function __construct(
        protected CoreContract $core,
        protected AssetManagerContract $assetManager,
    )
    {
        $this->booted();
    }

    /**
     * @return list<class-string<PageContract>>
     */
    abstract protected function pages(): array;

    public function getPages(): Pages
    {
        if (! is_null($this->pages)) {
            return $this->pages;
        }

        $this->pages = Pages::make($this->pages())
            ->map(fn(string $page) => $this->core->getContainer()->get($page))
            ->setResource($this);

        return $this->pages;
    }

    public function flushState(): void
    {
        //
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getRouter(): RouterContract
    {
        return (clone $this->core->getRouter())->withResource($this);
    }

    public function getUrl(): string
    {
        return $this->getRouter()
            ->withPage($this->getPages()->first())
            ->to('resource.page')
        ;
    }

    public function isActive(): bool
    {
        return $this->getRouter()->extractResourceUri() === $this->getUriKey();
    }
}
