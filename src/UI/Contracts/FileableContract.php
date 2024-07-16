<?php

declare(strict_types=1);

namespace MoonShine\UI\Contracts;

interface FileableContract
{
    public function disk(string $disk): static;

    public function getDisk(): string;

    public function options(array $options): static;

    public function getOptions(): array;

    public function dir(string $dir): static;

    public function getDir(): string;

    public function allowedExtensions(array $allowedExtensions): static;

    public function getAllowedExtensions(): array;

    public function isAllowedExtension(string $extension): bool;

    public function disableDownload(): static;

    public function canDownload(): bool;

    public function isDeleteFiles(): bool;
}
