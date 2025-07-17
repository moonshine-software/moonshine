<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\PagesContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\ResourcesContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @template TConfig of ConfiguratorContract = ConfiguratorContract
 * @template TFields of FieldsContract = FieldsContract
 * @template TRouter of RouterContract = RouterContract
 */
interface CoreContract
{
    public function runningUnitTests(): bool;

    public function runningInConsole(): bool;

    public function isLocal(): bool;

    public function isProduction(): bool;

    /**
     * @template T
     * @param class-string<T>|null $id
     * @param  mixed  ...$parameters
     *
     * @return ($id is null ? ContainerInterface : T)
     */
    public function getContainer(?string $id = null, mixed $default = null, ...$parameters): mixed;

    /**
     * @template TInstance
     * @param class-string<TInstance> $class
     * @return ?TInstance
     */
    public function getInstances(string $class): mixed;

    public function getRenderer(): ViewRendererContract;

    public function getRequest(): RequestContract;

    public function getCrudRequest(): CrudRequestContract;

    /**
     * @return TRouter
     */
    public function getRouter(): RouterContract;

    /**
     * @return ConfiguratorContract<TConfig>
     */
    public function getConfig(): ConfiguratorContract;

    public function getTranslator(): TranslatorContract;

    /**
     * @param mixed ...$parameters
     */
    public function getStorage(...$parameters): StorageContract;

    public function getCache(): CacheInterface;

    /**
     * @param  iterable<array-key, ComponentContract|FieldContract>  $items
     * @return TFields
     */
    public function getFieldsCollection(iterable $items = []): FieldsContract;

    /**
     * @param  list<class-string<ResourceContract>>  $data
     */
    public function resources(array $data, bool $newCollection = false): static;

    public function getResources(): ResourcesContract;

    /**
     * @param  list<class-string<PageContract>>  $data
     */
    public function pages(array $data, bool $newCollection = false): static;

    public function getPages(): PagesContract;

    public function autoload(?string $namespace = null): static;

    public function getOptimizer(): OptimizerCollectionContract;

    public function getAttributes(): CacheAttributesContract;
}
