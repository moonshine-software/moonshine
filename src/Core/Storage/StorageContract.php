<?php

declare(strict_types=1);

namespace MoonShine\Core\Storage;

interface StorageContract
{
    public function store(string $path, mixed $file = null, array $options = []): false|string;

    public function storeAs(string $path, mixed $file, $name = null, array $options = []): false|string;

    public function exists(string $path): bool;

    public function path(string $path): string;

    public function url(string $path): string;

    public function delete(string|array $paths): bool;

    public function files(string $directory, bool $recursive = false): array;

    public function directories(string $directory, bool $recursive = false): array;

    public function makeDirectory(string $path): bool;

    public function deleteDirectory(string $directory): bool;
}
