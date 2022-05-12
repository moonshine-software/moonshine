<?php

namespace Leeto\MoonShine\Contracts\Fields;

interface FileFieldContract
{
    public function disc(string $disc): static;

    public function getDisc(): string;

    public function dir(string $dir): static;

    public function getDir(): string;

    public function allowedExtension(array $allowedExtension): static;

    public function getAllowedExtension(): array;

    public function isAllowedExtension(string $extension): bool;

    public function disableDownload(): static;

    public function canDownload(): bool;

    public function removable(): static;

    public function isRemovable(): bool;
}