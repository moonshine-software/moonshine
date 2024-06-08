<?php

declare(strict_types=1);

namespace MoonShine\UI\Contracts\Actions;

use MoonShine\Core\Contracts\CastedData;

interface ActionButtonContract
{
    public function getUrl(mixed $data = null): string;

    public function isBulk(): bool;

    public function getData(): ?CastedData;

    public function setData(?CastedData $data = null): self;

    public function inDropdown(): bool;
}
