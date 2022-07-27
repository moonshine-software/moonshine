<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Contracts;

interface RenderableContract
{
    public static function make(...$arguments): static;

    public function id(string $index = null): string;

    public function name(string $index = null): string;

    public function label(): string;

    public function setLabel(string $label): static;

    public function getView(): string;
}
