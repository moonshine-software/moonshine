<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;

interface ActionButtonContract
{
    public function getUrl(mixed $data = null): string;

    public function isBulk(): bool;

    public function getData(): ?DataWrapperContract;

    public function setData(?DataWrapperContract $data = null): static;
}
