<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;

interface ActionButtonContract
{
    public function getUrl(mixed $data = null): string;

    public function isBulk(): bool;

    public function getData(): ?CastedDataContract;

    public function setData(?CastedDataContract $data = null): self;
}
