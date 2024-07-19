<?php

declare(strict_types=1);

namespace MoonShine\Core;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\DependencyInjection\RequestContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\Core\DependencyInjection\StorageContract;
use MoonShine\Contracts\Core\DependencyInjection\TranslatorContract;
use MoonShine\Contracts\Core\DependencyInjection\ViewRendererContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\ResourcesContract;
use MoonShine\Core\Pages\Pages;
use MoonShine\Core\Resources\Resources;
use MoonShine\Support\Memoize\MemoizeRepository;
use Psr\Container\ContainerInterface;

abstract class Core implements CoreContract
{
    use Conditionable;

    protected array $resources = [];

    protected array $pages = [];

    protected static Closure|CoreContract $instance;

    public function __construct(
        protected ContainerInterface $container,
        protected ViewRendererContract $viewRenderer,
        protected RouterContract $router,
        protected RequestContract $request,
        protected ConfiguratorContract $config,
        protected TranslatorContract $translator,
    ) {
        static::setInstance(
            fn (): mixed => $this->getContainer(CoreContract::class)
        );
    }

    public static function setInstance(Closure|CoreContract $core): void
    {
        static::$instance = $core;
    }

    public static function getInstance(): static
    {
        return value(static::$instance);
    }

    abstract public function runningUnitTests(): bool;

    abstract public function runningInConsole(): bool;

    abstract public function isLocal(): bool;

    abstract public function isProduction(): bool;

    /**
     * @template T
     * @param class-string<T>|null $id
     * @return T|ContainerInterface
     */
    abstract public function getContainer(?string $id = null, mixed $default = null, ...$parameters): mixed;

    abstract public function getStorage(...$parameters): StorageContract;

    public static function path(string $path = ''): string
    {
        $path = $path ? DIRECTORY_SEPARATOR . $path : $path;

        return realpath(dirname(__DIR__)) . '/../' . trim($path, '/');
    }

    public function getRenderer(): ViewRendererContract
    {
        return $this->viewRenderer;
    }

    public function getRequest(): RequestContract
    {
        // todo cuz Core is singleton
        return $this->getContainer(RequestContract::class);
    }

    public function getRouter(): RouterContract
    {
        return $this->router;
    }

    public function getConfig(): ConfiguratorContract
    {
        return $this->config;
    }

    public function getTranslator(): TranslatorContract
    {
        return $this->translator;
    }

    /**
     * @template-covariant T of FieldsContract
     * @return T
     */
    public function getFieldsCollection(iterable $items = []): FieldsContract
    {
        /** @var FieldsContract $collection */
        $collection = $this->container->get(FieldsContract::class);

        return $collection?->push(...$items);
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

        $this->getRouter()->flushState();

        MemoizeRepository::getInstance()->flush();
    }

    /**
     * Register resources in the system
     *
     * @param  list<class-string<ResourceContract>>  $data
     */
    public function resources(array $data, bool $newCollection = false): static
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
    public function getResources(): ResourcesContract
    {
        return Resources::make($this->resources)
            ->map(fn (string|ResourceContract $class): mixed => is_string($class) ? $this->getContainer()->get($class) : $class);
    }

    /**
     * Register pages in the system
     *
     * @param  list<class-string<PageContract>>  $data
     */
    public function pages(array $data, bool $newCollection = false): static
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
            ->map(fn (string|PageContract $class): mixed => is_string($class) ? $this->getContainer()->get($class) : $class);
    }
}
