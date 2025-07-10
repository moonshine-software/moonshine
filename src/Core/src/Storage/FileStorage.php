<?php

declare(strict_types=1);

namespace MoonShine\Core\Storage;

use MoonShine\Contracts\Core\DependencyInjection\StorageContract;
use Throwable;

final class FileStorage implements StorageContract
{
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function getPath(string $path): string
    {
        return $path;
    }

    public function getUrl(string $path): string
    {
        return $path;
    }

    public function delete(array|string $paths): bool
    {
        /** @var string[] $paths */
        $paths = \is_array($paths) ? $paths : \func_get_args();

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
        if (! \is_string($file)) {
            return false;
        }

        return move_uploaded_file($file, $path) ? $path : false;
    }

    public function storeAs(string $path, mixed $file, string|array|null $name = null, array $options = []): false|string
    {
        return $this->store($path, $file, $options);
    }

    public function getFiles(?string $directory = null, bool $recursive = false): array
    {
        if (! \is_string($directory)) {
            return [];
        }

        if (! is_dir($directory)) {
            return [];
        }

        $files = [];

        $items = scandir($directory);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $directory . DIRECTORY_SEPARATOR . $item;

            if (is_file($fullPath)) {
                $files[] = $fullPath;
            } elseif ($recursive && is_dir($fullPath)) {
                foreach ($this->getFiles($fullPath, true) as $subFile) {
                    $files[] = $subFile;
                }
            }
        }

        return $files;
    }

    public function getDirectories(?string $directory = null, bool $recursive = false): array
    {
        if (! \is_string($directory)) {
            return [];
        }

        if (! is_dir($directory)) {
            return [];
        }

        $dirs = [];

        $items = scandir($directory);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $directory . DIRECTORY_SEPARATOR . $item;

            if (is_dir($fullPath)) {
                $dirs[] = $fullPath;

                if ($recursive) {
                    foreach ($this->getDirectories($fullPath, true) as $subDir) {
                        $dirs[] = $subDir;
                    }
                }
            }
        }

        return $dirs;
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
