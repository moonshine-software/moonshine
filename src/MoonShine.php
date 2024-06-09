<?php

declare(strict_types=1);

namespace MoonShine;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Core\Contracts\PageContract;
use MoonShine\Core\Contracts\ResourceContract;
use MoonShine\Core\Pages\Pages;
use MoonShine\Core\Request;
use MoonShine\Core\Resources\Resources;
use MoonShine\Support\Enums\Env;
use MoonShine\Support\Memoize\MemoizeRepository;
use MoonShine\UI\Contracts\MoonShineRenderable;

class MoonShine
{
    use Conditionable;

    private static Closure $renderer;

    private static Closure $container;

    private static Env $env = Env::LOCAL;

    private static Closure $request;

    private static Closure $flushStates;

    private array $resources = [];

    private array $pages = [];

    public static function path(string $path = ''): string
    {
        $path = $path ? DIRECTORY_SEPARATOR . $path : $path;

        return realpath(dirname(__DIR__) . $path);
    }

    public static function setEnv(Env $env): void
    {
        self::$env = $env;
    }

    public function runningUnitTests(): bool
    {
        return  self::$env === Env::TESTING;
    }

    public function runningInConsole(): bool
    {
        return $this->runningUnitTests() || self::$env === Env::CONSOLE;
    }

    public function isLocal(): bool
    {
        return  self::$env === Env::LOCAL;
    }

    public function isProduction(): bool
    {
        return  self::$env === Env::PRODUCTION;
    }

    /**
     * @param  Closure(string $view, array $data, MoonShineRenderable $object): string  $renderer
     */
    public static function renderUsing(Closure $renderer): void
    {
        self::$renderer = $renderer;
    }

    public function render(string $view, array $data, object $object): mixed
    {
        return value(self::$renderer, $view, $data, $object);
    }

    /**
     * @param  Closure(string $id, ...$parameters): mixed  $container
     */
    public static function containerUsing(Closure $container): void
    {
        self::$container = $container;
    }

    public static function getInstance(): self
    {
        return value(self::$container, MoonShine::class);
    }

    public function getContainer(string $id, mixed $default = null, ...$parameters): mixed
    {
        return value(self::$container, $id, $default, $parameters);
    }

    /**
     * @param  Closure(string $key, mixed $default): mixed  $request
     */
    public static function requestUsing(mixed $request): void
    {
        self::$request = $request;
    }

    public function getRequest(): Request
    {
        return value(self::$request);
    }

    public static function flushStates(Closure $flushStates): void
    {
        self::$flushStates = $flushStates;
    }

    public function flushState(): void
    {
        $this->getResources()->transform(static function (ResourceContract $resource): ResourceContract {
            $resource->flushState();

            return $resource;
        });

        $this->getPages()->transform(static function (PageContract $page): PageContract {
            $page->flushState();

            return $page;
        });

        moonshineRouter()->flushState();

        MemoizeRepository::getInstance()->flush();

        value(self::$flushStates, $this);
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
            ->map(fn (string|ResourceContract $class): mixed => is_string($class) ? $this->getContainer($class) : $class);
    }

    /**
     * Register pages in the system
     *
     * @param  list<class-string<PageContract>>  $data
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
            ->map(fn (string|PageContract $class): mixed => is_string($class) ? $this->getContainer($class) : $class);
    }
}
