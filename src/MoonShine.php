<?php

declare(strict_types=1);

namespace MoonShine;

use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Pages\Page;
use MoonShine\Pages\Pages;
use MoonShine\Resources\Resources;
use MoonShine\Support\MemoizeRepository;

class MoonShine
{
    use Conditionable;

    private array $resources = [];

    private array $pages = [];

    public function __construct(
        private MoonShineConfigurator $config
    ) {
    }

    /**
     * @param  mixed|null  $default
     * @return mixed|MoonShineConfigurator
     */
    public function config(?string $key = null, mixed $default = null): mixed
    {
        if (! is_null($key)) {
            return $this->config->get($key, $default);
        }

        return $this->config;
    }

    public function flushState(): void
    {
        $this->getResources()->transform(function (ResourceContract $resource): ResourceContract {
            $resource->flushState();

            return $resource;
        });

        $this->getPages()->transform(function (Page $page): Page {
            $page->flushState();

            return $page;
        });

        moonshineCache()->flush();
        moonshineRouter()->flushState();

        MemoizeRepository::getInstance()->flush();
    }

    public static function path(string $path = ''): string
    {
        return realpath(
            dirname(__DIR__) . ($path ? DIRECTORY_SEPARATOR . $path : $path)
        );
    }

    /**
     * Register resources in the system
     *
     * @param  list<class-string<ResourceContract>>  $data
     */
    public function resources(array $data, bool $newCollection = false): self
    {
        if ($newCollection) {
            $this->resources = [];
        }

        $this->resources = array_merge(
            $this->resources,
            $data
        );

        return $this;
    }

    /**
     * Get collection of registered resources
     *
     * @return Resources<int, ResourceContract>
     */
    public function getResources(): Resources
    {
        return Resources::make($this->resources)
            ->map(fn (string|ResourceContract $class) => is_string($class) ? app($class) : $class);
    }

    /**
     * Register pages in the system
     *
     * @param  list<class-string<Page>>  $data
     */
    public function pages(array $data, bool $newCollection = false): self
    {
        if ($newCollection) {
            $this->pages = [];
        }

        $this->pages = array_merge(
            $this->pages,
            $data
        );

        return $this;
    }

    /**
     * Get collection of registered pages
     */
    public function getPages(): Pages
    {
        return Pages::make($this->pages)
            ->except('error')
            ->map(fn (string|Page $class) => is_string($class) ? app($class) : $class);
    }
}
