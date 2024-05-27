<?php

declare(strict_types=1);

namespace MoonShine;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\MoonShineRenderable;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Core\Pages\Page;
use MoonShine\Core\Pages\Pages;
use MoonShine\Core\Resources\Resources;
use MoonShine\Support\Enums\Env;
use MoonShine\Support\Memoize\MemoizeRepository;

class MoonShine
{
    use Conditionable;

    private static Closure $renderer;

    private static Closure $container;

    private static Env $env = Env::LOCAL;

    private static Closure $request;

    private array $resources = [];

    private array $pages = [];

    public function __construct(
        private MoonShineConfigurator $config
    ) {

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
        return $this->runningUnitTests() ||  self::$env === Env::CONSOLE;
    }

    public function isLocal(): bool
    {
        return  self::$env === Env::LOCAL;
    }

    public function isProduction(): bool
    {
        return  self::$env=== Env::PRODUCTION;
    }

    /**
     * @param  Closure(string $view, array $data, MoonShineRenderable $object): string  $renderer
     * @return void
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
     * @return void
     */
    public static function containerUsing(Closure $container): void
    {
        self::$container = $container;
    }

    public static function getInstance(): self
    {
        return value(self::$container, MoonShine::class);
    }

    public function getContainer(string $id, ...$parameters): mixed
    {
        return value(self::$container, $id, $parameters);
    }

    /**
     * @param  Closure(string $key, mixed $default): mixed  $request
     * @return void
     */
    public static function requestUsing(Closure $request): void
    {
        self::$request = $request;
    }

    public function getRequest(string $key, mixed $default = null): mixed
    {
        return value(self::$request, $key, $default);
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
            ->map(fn (string|ResourceContract $class) => is_string($class) ? $this->getContainer($class) : $class);
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
            ->map(fn (string|Page $class) => is_string($class) ? $this->getContainer($class) : $class);
    }
}
