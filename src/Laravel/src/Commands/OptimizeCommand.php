<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Commands;

use Attribute;
use Illuminate\Filesystem\Filesystem;
use LogicException;
use MoonShine\Contracts\Core\DependencyInjection\CacheAttributesContract;
use MoonShine\Contracts\Core\DependencyInjection\OptimizerCollectionContract;
use MoonShine\Contracts\MenuManager\MenuAutoloaderContract;
use MoonShine\Contracts\MenuManager\MenuElementContract;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(name: 'moonshine:optimize')]
class OptimizeCommand extends MoonShineCommand
{
    protected $signature = 'moonshine:optimize';

    protected $description = 'Cache MoonShine pages and resources to increase performance';

    public function handle(
        OptimizerCollectionContract $optimizer,
        Filesystem $files,
        MenuAutoloaderContract $menuAutoloader,
        CacheAttributesContract $cacheAttributes,
    ): int {
        $this->components->info('Caching MoonShine pages and resources.');

        $filename = $optimizer->getCachePath();

        $this->store($files, $filename, $this->getFreshSources($optimizer, $menuAutoloader, $cacheAttributes));

        $this->validateCache($files, $filename);

        $this->components->info('MoonShine cached successfully.');

        return self::SUCCESS;
    }

    /**
     * @return array<class-string, array<array-key, mixed>>
     */
    protected function getFreshSources(OptimizerCollectionContract $optimizer, MenuAutoloaderContract $menuAutoloader, CacheAttributesContract $cacheAttributes): array
    {
        return [
            ...$optimizer->getTypes($this->getNamespace(), false),
            MenuElementContract::class => $menuAutoloader->toArray(),
            Attribute::class => $cacheAttributes->resolve(),
        ];
    }

    /**
     * @param  Filesystem  $storage
     * @param  string  $cachePath
     * @param  array<class-string, array<array-key, mixed>>  $sources
     *
     * @return void
     */
    protected function store(Filesystem $storage, string $cachePath, array $sources): void
    {
        $storage->put(
            $cachePath,
            '<?php return ' . var_export($sources, true) . ';' . PHP_EOL,
        );
    }

    protected function validateCache(Filesystem $files, string $filename): void
    {
        try {
            require $filename;
        } catch (Throwable $e) {
            $files->delete($filename);

            throw new LogicException('Your MoonShine file are not serializable.', 0, $e);
        }
    }
}
