<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits;

use Illuminate\Support\Str;
use SplFileInfo;

trait WithStorage
{
    protected ?string $disk = null;

    /**
     * @var string[]|null
     */
    protected ?array $options = null;

    protected string $dir = '/';

    /**
     * @param  string  $dir
     */
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

    /**
     * @param  string  $disk
     */
    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * @return   string
     */
    public function getDisk(): string
    {
        return $this->disk ?? $this->getCore()->getConfig()->getDisk();
    }

    /**
     * @param  string[]  $options
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return non-empty-array<string, string>
     */
    public function getOptions(): array
    {
        return [
            ...$this->options ?? $this->getCore()->getConfig()->getDiskOptions(),
            'disk' => $this->getDisk(),
        ];
    }

    /**
     * @return   string
     */
    public function getDir(): string
    {
        return Str::of($this->dir)
            ->trim('/')
            ->value();
    }

    /**
     * @param  string  $value
     * @return   string
     */
    public function getStorageUrl(string $value): string
    {
        return $this->getCore()->getStorage(disk: $this->getDisk())->getUrl($value);
    }

    /**
     * @param  string|string[]  $path
     *
     */
    public function deleteStorageFile(string|array $path): bool
    {
        return $this->getCore()->getStorage(disk: $this->getDisk())->delete($path);
    }

    /**
     * @param  string  $dir
     */
    public function deleteStorageDirectory(string $dir): bool
    {
        return $this->getCore()->getStorage(disk: $this->getDisk())->deleteDirectory($dir);
    }

    /**
     * @param string $dir
     * @return string[]
     */
    public function getStorageDirectories(string $dir): array
    {
        return $this->getCore()->getStorage(disk: $this->getDisk())->getDirectories($dir);
    }

    /**
     * @param string $dir
     * @return list<SplFileInfo>
     */
    public function getStorageFiles(string $dir): array
    {
        return $this->getCore()->getStorage(disk: $this->getDisk())->getFiles($dir);
    }
}
