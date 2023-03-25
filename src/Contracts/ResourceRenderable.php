<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts;

interface ResourceRenderable
{
    public function id(string $index = null): string;

    public function name(string $index = null): string;

    public function label(): string;

    public function getView(): string;
}
