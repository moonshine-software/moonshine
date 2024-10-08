<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core;

interface StatefulContract
{
    public function flushState(): void;
}
