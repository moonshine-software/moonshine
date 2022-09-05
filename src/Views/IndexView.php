<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Views;

use JsonSerializable;
use Leeto\MoonShine\Contracts\HasEndpoint;
use Leeto\MoonShine\Contracts\ViewContract;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\ViewComponents\ViewComponents;

final class IndexView implements HasEndpoint, ViewContract, JsonSerializable
{
    use Makeable;

    public function __construct(
        protected ViewComponents $components
    ) {
    }

    public function endpoint(): string
    {
        return '';
    }

    public function components(): ViewComponents
    {
        return $this->components;
    }

    public function jsonSerialize(): array
    {
        return [
            'endpoint' => $this->endpoint(),
            'component' => '',
            'components' => $this->components(),
        ];
    }
}
