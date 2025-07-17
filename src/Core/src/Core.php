<?php

declare(strict_types=1);

namespace MoonShine\Core;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\AssetManager\AssetManagerContract;
use MoonShine\Contracts\Core\DependencyInjection\CacheAttributesContract;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\CrudRequestContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\DependencyInjection\OptimizerCollectionContract;
use MoonShine\Contracts\Core\DependencyInjection\RequestContract;
use MoonShine\Contracts\Core\DependencyInjection\RouterContract;
use MoonShine\Contracts\Core\DependencyInjection\StorageContract;
use MoonShine\Contracts\Core\DependencyInjection\TranslatorContract;
use MoonShine\Contracts\Core\DependencyInjection\ViewRendererContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\ResourcesContract;
use MoonShine\Contracts\Core\StatefulContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Core\Pages\Pages;
use MoonShine\Core\Resources\Resources;
use MoonShine\Support\Memoize\MemoizeRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @template TConfig of ConfiguratorContract = ConfiguratorContract
 * @template TFields of FieldsContract = FieldsContract
 * @template TRouter of RouterContract = RouterContract
 *
 * @implements CoreContract<TConfig, TFields, TRouter>
 */
abstract class Core implements CoreContract, StatefulContract
{
    use Conditionable;

    /**
     * @var list<class-string<ResourceContract>|ResourceContract>
     */
    protected array $resources = [];

    /**
     * @var list<class-string<PageContract>|PageContract>
     */
    protected array $pages = [];

    /**
     * @var array<class-string, ResourceContract|PageContract>
     */
    protected array $instances = [];

    /**
     * @var (Closure(): CoreContract<ConfiguratorContract>)|CoreContract<ConfiguratorContract>
     */
    protected static Closure|CoreContract $instance;

    /**
     * @param  TRouter  $router
     */
    public function __construct(
        protected ContainerInterface $container,
        protected ViewRendererContract $viewRenderer,
        protected RouterContract $router,
        protected ConfiguratorContract $config,
        protected TranslatorContract $translator,
        protected CacheInterface $cache,
        protected OptimizerCollectionContract $optimizer,
    ) {
        static::setInstance(
            function (): CoreContract {
                /** @var CoreContract<TConfig> $core */
                $core = $this->getContainer(CoreContract::class);

                return $core;
            }
        );
    }

    /**
     * @param (Closure(): CoreContract<ConfiguratorContract>)|CoreContract<ConfiguratorContract> $core
     */
    public static function setInstance(Closure|CoreContract $core): void
    {
        static::$instance = $core;
    }

    /**
     * @return CoreContract<TConfig>
     */
    public static function getInstance(): CoreContract
    {
        /**
         * @var CoreContract<TConfig>
         */
        return value(static::$instance);
    }

    abstract public function runningUnitTests(): bool;

    abstract public function runningInConsole(): bool;

    abstract public function isLocal(): bool;

    abstract public function isProduction(): bool;

    /**
     * @template T
     * @param class-string<T>|null $id
     * @param  mixed  ...$parameters
     *
     * @return ($id is null ? ContainerInterface : T)
     */
    abstract public function getContainer(?string $id = null, mixed $default = null, ...$parameters): mixed;

    abstract public function getStorage(...$parameters): StorageContract;

    public static function path(string $path = ''): string
    {
        $path = $path ? DIRECTORY_SEPARATOR . $path : $path;

        return realpath(\dirname(__DIR__)) . '/../' . trim($path, '/');
    }

    public function getRenderer(): ViewRendererContract
    {
        return $this->viewRenderer;
    }

    public function getRequest(): RequestContract
    {
        /**
         * @var RequestContract
         */
        return $this->getContainer(RequestContract::class);
    }

    public function getCrudRequest(): CrudRequestContract
    {
        /**
         * @var CrudRequestContract
         */
        return $this->getContainer(CrudRequestContract::class);
    }

    /**
     * @return TRouter
     */
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
     * @param  iterable<array-key, FieldContract>  $items
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return TFields
     */
    public function getFieldsCollection(iterable $items = []): FieldsContract
    {
        /** @var TFields $collection */
        $collection = $this->container->get(FieldsContract::class);

        /** @var TFields */
        return $collection->push(...$items);
    }

    public function flushState(): void
    {
        $this->instances = [];

        $this->getResources()->transform(static function (ResourceContract $resource): ResourceContract {
            $resource->flushState();

            return $resource;
        });

        $this->getPages()->transform(static function (PageContract $page): PageContract {
            $page->flushState();

            return $page;
        });

        $this->getRouter()->flushState();

        $this->getContainer(AssetManagerContract::class)->flushState();
        $this->getContainer(MenuManagerContract::class)->flushState();

        MemoizeRepository::getInstance()->flushState();
    }

    /**
     * @template T of ResourceContract|PageContract
     * @param  iterable<class-string<T>|T>  $items
     * @return list<T>
     */
    private function resolveInstances(iterable $items): array
    {
        $targets = [];

        foreach ($items as $item) {
            if (\is_string($item) && isset($this->instances[$item])) {
                /** @var T $instance */
                $instance = $this->instances[$item];

                $targets[] = $instance;

                continue;
            }

            /** @var T $instance */
            $instance = \is_string($item) ? $this->getContainer()->get($item) : $item;
            $this->instances[$instance::class] = $instance;
            $targets[] = $instance;
        }

        return $targets;
    }

    /**
     * @template T of ResourceContract|PageContract
     * @param  class-string<T>  $class
     * @return T|null
     */
    public function getInstances(string $class): mixed
    {
        /** @var T|null */
        return $this->instances[$class] ?? $this->getContainer($class);
    }

    /**
     * Register resources in the system
     *
     * @param  list<class-string<ResourceContract>|ResourceContract>  $data
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
     * @return Resources<array-key, ResourceContract>
     */
    public function getResources(): ResourcesContract
    {
        /** @var list<ResourceContract> $resources */
        $resources = $this->resolveInstances(
            $this->resources
        );

        return Resources::make($resources);
    }

    /**
     * Register pages in the system
     *
     * @param  list<class-string<PageContract>|PageContract>  $data
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
     *
     * @return Pages<PageContract>
     */
    public function getPages(): Pages
    {
        /** @var list<PageContract> $pages */
        $pages = $this->resolveInstances(
            (new Collection($this->pages))->except('error')
        );

        return Pages::make($pages);
    }

    public function getOptimizer(): OptimizerCollectionContract
    {
        return $this->optimizer;
    }

    public function getCache(): CacheInterface
    {
        return $this->cache;
    }

    public function getAttributes(): CacheAttributesContract
    {
        return $this->getContainer(CacheAttributesContract::class);
    }

    /**
     * @api
     */
    public function simulateRoute(?PageContract $page = null, ?ResourceContract $resource = null): static
    {
        return $this;
    }
}
