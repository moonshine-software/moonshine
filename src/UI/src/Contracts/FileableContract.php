<?php

declare(strict_types=1);

namespace MoonShine\UI\Contracts;

interface FileableContract
{
    /**
     * @param  string  $disk
     */
    public function disk(string $disk): static;

    public function getDisk(): string;

    /**
     * @param  string[]  $options
     */
    public function options(array $options): static;

    /**
     * @return string[]
     */
    public function getOptions(): array;

    /**
     * @param  string  $dir
     */
    public function dir(string $dir): static;

    /**
     * @return  string
     */
    public function getDir(): string;

    /**
     * @param  string[]  $allowedExtensions
     */
    public function allowedExtensions(array $allowedExtensions): static;

    /**
     * @return   string[]
     */
    public function getAllowedExtensions(): array;

    /**
     * @param  string $extension
     */
    public function isAllowedExtension(string $extension): bool;

    public function disableDownload(): static;

    public function canDownload(): bool;

    public function isDeleteFiles(): bool;
}
