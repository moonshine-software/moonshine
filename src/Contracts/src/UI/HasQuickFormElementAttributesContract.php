<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;

interface HasQuickFormElementAttributesContract
{
    public function setNameAttribute(string $name): static;

    public function getNameAttribute(string $index = null): string;

    public function wrapName(string $wrapName): static;

    public function getWrapName(): ?string;

    public function generateNameFrom(?string ...$values): string;

    public function getNameDot(): string;

    public function setNameIndex(int|string $key, int $index = 0): static;

    public function setId(string $id): static;

    public function required(Closure|bool|null $condition = null): static;

    public function disabled(Closure|bool|null $condition = null): static;

    public function readonly(Closure|bool|null $condition = null): static;
}
