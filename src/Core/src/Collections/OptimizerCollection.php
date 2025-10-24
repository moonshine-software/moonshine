<?php

declare(strict_types=1);

namespace MoonShine\Core\Collections;

use Composer\Autoload\ClassLoader;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\ConfiguratorContract;
use MoonShine\Contracts\Core\DependencyInjection\OptimizerCollectionContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\ResourceContract;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use ReflectionClass;
use ReflectionException;

final class OptimizerCollection implements OptimizerCollectionContract
{
    /**
     * Contains links to interfaces for searching and grouping elements.
     *
     * @var array<class-string>
     */
    protected array $groups = [
        PageContract::class,
        ResourceContract::class,
        MenuElementContract::class,
    ];

    /**
     * Contains the processed result of the found groups of elements.
     *
     * @var array<class-string>|null
     */
    protected ?array $types = null;

    public function __construct(
        protected string $cachePath,
        protected ConfiguratorContract $config,
    ) {
    }

    public function getTypes(?string $namespace = null, bool $withCache = true): array
    {
        return $this->types ??= $this->getDetected($namespace, $withCache);
    }

    public function getType(string $contract, ?string $namespace = null, bool $withCache = true): array
    {
        return $this->getTypes($namespace, $withCache)[$contract] ?? [];
    }

    public function hasType(string $contract): bool
    {
        return $this->getType($contract) !== [];
    }

    public function hasCache(): bool
    {
        return file_exists($this->getCachePath());
    }

    public function getCachePath(): string
    {
        return $this->cachePath;
    }

    protected function getDetected(?string $namespace, bool $withCache): array
    {
        if ($withCache && file_exists($path = $this->getCachePath())) {
            return require $path;
        }

        return $this->getMerged(
            $this->getPages(),
            $this->getFiltered($namespace)
        );
    }

    /**
     * @param  array<array<string, mixed>>  ...$items
     *
     * @return array
     */
    protected function getMerged(array ...$items): array
    {
        $autoload = [];

        foreach ($this->groups as $type) {
            foreach ($items as $value) {
                $autoload[$type] = array_unique(array_merge($autoload[$type] ?? [], $value[$type] ?? []));
            }
        }

        return array_filter($autoload);
    }

    protected function getPages(): array
    {
        return [PageContract::class => $this->config->getPages()];
    }

    protected function getFiltered(?string $namespace): array
    {
        return Collection::make(ClassLoader::getRegisteredLoaders())
            ->map(
                fn (ClassLoader $loader) => Collection::make($loader->getClassMap())
                    ->when($namespace, static fn (Collection $items) => $items->filter(
                        static fn (string $path, string $class): bool => str_starts_with($class, (string) $namespace)
                    ))
                    ->flip()
                    ->values()
                    ->filter(fn (string $class): bool => $this->isInstanceOf($class, $this->groups)
                        && $this->isNotAbstract($class))
            )
            ->collapse()
            ->groupBy(fn (string $class): string => $this->getGroupName($class))
            ->toArray();
    }

    protected function getGroupName(string $class): string
    {
        foreach ($this->groups as $contract) {
            if ($this->isInstanceOf($class, $contract)) {
                return $contract;
            }
        }

        return '';
    }

    /**
     * @param  class-string  $haystack
     * @param  list<class-string>|string  $needles
     *
     * @return bool
     */
    protected function isInstanceOf(string $haystack, array|string $needles): bool
    {
        foreach (Arr::wrap($needles) as $needle) {
            if (is_a($haystack, $needle, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  class-string  $class
     *
     * @throws ReflectionException
     * @return bool
     */
    protected function isNotAbstract(string $class): bool
    {
        return ! (new ReflectionClass($class))->isAbstract();
    }
}
