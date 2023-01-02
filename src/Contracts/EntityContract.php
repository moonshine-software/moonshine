<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts;

interface EntityContract
{
    public function id(): int|string;

    public function attributes(string $key = null): mixed;

    public function actions(): array;
}
