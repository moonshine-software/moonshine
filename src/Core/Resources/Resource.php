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
use MoonShine\Core\Traits\WithCore;
use MoonShine\Core\Traits\WithUriKey;

abstract class Resource implements ResourceContract, MenuFillerContract
{
    use WithCore;
    use WithUriKey;
    use WithAssets;

    protected string $title = '';

    protected ?Pages $pages = null;

    protected bool $booted = false;

    protected bool $loaded = false;

    public function __construct(
        CoreContract $core,
    ) {
        $this->setCore($core);
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
            ->map(fn (string $page) => $this->getCore()->getContainer()->get($page))
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

    // todo(hot)-3 ??? Rename or refactor
    public function getRouter(): RouterContract
    {
        return (clone $this->getCore()->getRouter())->withResource($this);
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
        return $this->getCore()->getRouter()->extractResourceUri() === $this->getUriKey();
    }
}
