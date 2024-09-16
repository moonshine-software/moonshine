<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\PagesContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\Core\ResourcesContract;
use Psr\Container\ContainerInterface;

/**
 * @template TConfig of ConfiguratorContract
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
     * @return T|ContainerInterface
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

    public function getRouter(): RouterContract;

    /**
     * @return ConfiguratorContract<TConfig>
     */
    public function getConfig(): ConfiguratorContract;

    public function getTranslator(): TranslatorContract;

    public function getStorage(...$parameters): StorageContract;

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

    public function flushState(): void;
}
