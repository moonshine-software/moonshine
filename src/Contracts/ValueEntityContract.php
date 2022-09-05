<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts;

interface ValueEntityContract
{
    public function id(): int;

    public function attributes(string $key = null): mixed;

    public function actions(): array;
}
