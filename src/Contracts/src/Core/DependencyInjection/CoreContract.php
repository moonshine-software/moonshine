<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\PagesContract;
use MoonShine\Contracts\Core\ResourcesContract;
use Psr\Container\ContainerInterface;

interface CoreContract
{
    /**
     * @template-covariant T
     * @param class-string<T>|null $id
     * @return T|ContainerInterface
     */
    public function getContainer(?string $id = null, mixed $default = null, ...$parameters): mixed;

    /**
     * @template-covariant TInstance
     * @param class-string<TInstance> $class
     * @return ?TInstance
     */
    public function getInstances(string $class): mixed;

    public function getRenderer(): ViewRendererContract;

    public function getRequest(): RequestContract;

    public function getRouter(): RouterContract;

    public function getConfig(): ConfiguratorContract;

    public function getTranslator(): TranslatorContract;

    /**
     * @template-covariant TCollection of FieldsContract
     * @return TCollection
     */
    public function getFieldsCollection(iterable $items = []): FieldsContract;

    /**
     * @param  list<class-string<ResourcesContract>>  $data
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
