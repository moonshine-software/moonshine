<?php

declare(strict_types=1);

namespace MoonShine\Laravel\DependencyInjection;

use Illuminate\Foundation\Application;
use MoonShine\Contracts\Core\DependencyInjection\StorageContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Core\Core;
use MoonShine\Core\Storage\FileStorage;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;

/**
 * @extends Core<MoonShineConfigurator, Fields, MoonShineRouter>
 */
final class MoonShine extends Core
{
    public static function path(string $path = ''): string
    {
        $path = $path ? DIRECTORY_SEPARATOR . $path : $path;

        return realpath(\dirname(__DIR__)) . '/../' . trim($path, '/');
    }

    public static function UIPath(string $path = ''): string
    {
        return self::path("/../UI$path");
    }

    public static function crudPath(string $path = ''): string
    {
        return self::path("/../Crud/src$path");
    }

    public function runningUnitTests(): bool
    {
        return $this->getContainer()->runningUnitTests();
    }

    public function runningInConsole(): bool
    {
        return $this->getContainer()->runningInConsole();
    }

    public function isLocal(): bool
    {
        return $this->getContainer()->isLocal();
    }

    public function isProduction(): bool
    {
        return $this->getContainer()->isProduction();
    }

    /**
     * @template T
     * @param class-string<T>|null $id
     * @param  mixed  ...$parameters
     *
     * @return ($id is null ? Application : T)
     */
    public function getContainer(?string $id = null, mixed $default = null, ...$parameters): mixed
    {
        if (! \is_null($id)) {
            return app()->make($id, $parameters) ?? $default;
        }

        return $this->container;
    }

    public function getStorage(...$parameters): StorageContract
    {
        return app()->make(StorageContract::class, $parameters) ?? new FileStorage();
    }

    public function flushState(): void
    {
        parent::flushState();

        ModelRelationField::$excludeInstancing = [];
    }

    public function autoload(?string $namespace = null): static
    {
        $namespace ??= $this->getConfig()->getNamespace();

        $pages = $this->getOptimizer()->getType(PageContract::class, $namespace);
        $resources = $this->getOptimizer()->getType(ResourceContract::class, $namespace);

        return $this
            ->pages($pages)
            ->resources($resources);
    }
}
