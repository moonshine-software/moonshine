<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts;

interface HasEndpoint
{
    public function endpoint(): string;
}
