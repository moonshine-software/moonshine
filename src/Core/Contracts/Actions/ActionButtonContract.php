<?php

declare(strict_types=1);

namespace MoonShine\Core\Contracts\Actions;

interface ActionButtonContract
{
    public function getUrl(mixed $data = null): string;

    public function isBulk(): bool;

    public function getItem(): mixed;

    public function setItem(mixed $item): self;

    public function inDropdown(): bool;
}
