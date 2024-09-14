<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

interface HasShowWhenContract
{
    public function hasShowWhen(): bool;

    public function getShowWhenCondition(): array;

    public function modifyShowFieldName(string $name): static;

    public function showWhen(
        string $column,
        mixed $operator = null,
        mixed $value = null
    ): static;

    public function showWhenDate(
        string $column,
        mixed $operator = null,
        mixed $value = null
    ): static;
}
