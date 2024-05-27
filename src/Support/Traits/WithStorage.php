<?php

declare(strict_types=1);

namespace MoonShine\Support\Traits;

use Illuminate\Support\Facades\Storage;

// TODO @isolate
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
        if (! Storage::disk($this->getDisk())->exists($this->getDir())) {
            Storage::disk($this->getDisk())->makeDirectory($this->getDir());
        }
    }

    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function getDisk(): string
    {
        return $this->disk ?? moonshineConfig()->getDisk();
    }

    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options ?? moonshineConfig()->getDiskOptions();
    }

    public function parseOptions(): array
    {
        return [
            ...$this->getOptions(),
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
        return Storage::disk($this->getDisk())->url($value);
    }

    public function deleteStorageFile(string|array $path): bool
    {
        return Storage::disk($this->getDisk())->delete($path);
    }

    public function deleteStorageDirectory(string $dir): bool
    {
        return Storage::disk($this->getDisk())->deleteDirectory($dir);
    }

    public function getStorageDirectories(string $dir): array
    {
        return Storage::disk($this->getDisk())->directories($dir);
    }

    public function getStorageFiles(string $dir): array
    {
        return Storage::disk($this->getDisk())->files($dir);
    }
}
