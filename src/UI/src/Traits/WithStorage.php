<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

trait WithStorage
{
    protected ?string $disk = null;

    protected ?array $options = null;

    protected string $dir = '/';

    public function dir(string $dir): static
    {
        $this->dir = $dir;

        return $this;
    }

    protected function resolveStorage(): void
    {
        if (! $this->getCore()->getStorage(disk: $this->getDisk())->exists($this->getDir())) {
            $this->getCore()->getStorage(disk: $this->getDisk())->makeDirectory($this->getDir());
        }
    }

    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function getDisk(): string
    {
        return $this->disk ?? $this->getCore()->getConfig()->getDisk();
    }

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        return [
            ...$this->options ?? $this->getCore()->getConfig()->getDiskOptions(),
            'disk' => $this->getDisk(),
        ];
    }

    public function getDir(): string
    {
        return str($this->dir)
            ->trim('/')
            ->value();
    }

    public function getStorageUrl(string $value): string
    {
        return $this->getCore()->getStorage(disk: $this->getDisk())->getUrl($value);
    }

    public function deleteStorageFile(string|array $path): bool
    {
        return $this->getCore()->getStorage(disk: $this->getDisk())->delete($path);
    }

    public function deleteStorageDirectory(string $dir): bool
    {
        return $this->getCore()->getStorage(disk: $this->getDisk())->deleteDirectory($dir);
    }

    public function getStorageDirectories(string $dir): array
    {
        return $this->getCore()->getStorage(disk: $this->getDisk())->getDirectories($dir);
    }

    public function getStorageFiles(string $dir): array
    {
        return $this->getCore()->getStorage(disk: $this->getDisk())->getFiles($dir);
    }
}
