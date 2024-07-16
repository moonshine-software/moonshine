<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Storage;

use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use MoonShine\Contracts\Core\DependencyInjection\StorageContract;

final readonly class LaravelStorage implements StorageContract
{
    private Filesystem $filesystem;

    public function __construct(
        private string $disk,
        private Factory $factory,
    ) {
        $this->filesystem = $this->factory->disk($this->disk);
    }

    public function store(string $path, mixed $file = null, array $options = []): false|string
    {
        return $this->filesystem->putFile($path, $file, $options);
    }

    public function storeAs(string $path, mixed $file, $name = null, array $options = []): false|string
    {
        return $this->filesystem->putFileAs($path, $file, $name, $options);
    }

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    public function getPath(string $path): string
    {
        return $this->filesystem->path($path);
    }

    public function getUrl(string $path): string
    {
        return $this->filesystem->url($path);
    }

    public function delete(array|string $paths): bool
    {
        return $this->filesystem->delete($paths);
    }

    public function getFiles(string $directory, bool $recursive = false): array
    {
        return $this->filesystem->files($directory, $recursive);
    }

    public function getDirectories(string $directory = null, bool $recursive = false): array
    {
        return $this->filesystem->directories($directory, $recursive);
    }

    public function makeDirectory(string $path): bool
    {
        return $this->filesystem->makeDirectory($path);
    }

    public function deleteDirectory(string $directory): bool
    {
        return $this->filesystem->deleteDirectory($directory);
    }
}
