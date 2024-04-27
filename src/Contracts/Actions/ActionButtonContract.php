<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Actions;

interface ActionButtonContract
{
    public function url(mixed $data = null): string;

    public function isBulk(): bool;

    public function getItem(): mixed;

    public function setItem(mixed $item): self;

    public function inDropdown(): bool;
}
