<?php

declare(strict_types=1);

namespace MoonShine\Core\Storage;

use Symfony\Component\Finder\Finder;
use Throwable;

final class FileStorage implements StorageContract
{
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function path(string $path): string
    {
        return $path;
    }

    public function url(string $path): string
    {
        return $path;
    }

    public function delete(array|string $paths): bool
    {
        $paths = is_array($paths) ? $paths : func_get_args();

        $success = true;

        foreach ($paths as $path) {
            try {
                if (@unlink($path)) {
                    clearstatcache(false, $path);
                } else {
                    $success = false;
                }
            } catch (Throwable) {
                $success = false;
            }
        }

        return $success;
    }

    public function store(string $path, mixed $file = null, array $options = []): false|string
    {
        return move_uploaded_file($file, $path);
    }

    public function storeAs(string $path, mixed $file, $name = null, array $options = []): false|string
    {
        return $this->store($path, $file, $options);
    }

    public function files(string $directory = null, bool $recursive = false): array
    {
        return iterator_to_array(
            Finder::create()->files()->in($directory)->depth(0)->sortByName(),
            false
        );
    }

    public function directories(string $directory = null, bool $recursive = false): array
    {
        $directories = [];

        foreach (Finder::create()->in($directory)->directories()->depth(0)->sortByName() as $dir) {
            $directories[] = $dir->getPathname();
        }

        return $directories;
    }

    public function makeDirectory(string $path): bool
    {
        return @mkdir($path);
    }

    public function deleteDirectory(string $directory): bool
    {
        return @rmdir($directory);
    }
}
