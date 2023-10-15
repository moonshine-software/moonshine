<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Actions;

interface ActionButtonContract
{
    public function url(): string;

    public function isBulk(): bool;

    public function getItem(): mixed;

    public function setItem(mixed $item): self;

    public function isSee(mixed $data): bool;

    public function inDropdown(): bool;
}
