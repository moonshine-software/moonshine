<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

interface HasShowWhenContract
{
    public function hasShowWhen(): bool;

    /**
     * @return array<string, string>
     */
    public function getShowWhenCondition(): array;

    public function modifyShowFieldName(string $name): static;

    public function modifyShowFieldRangeName(): static;

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
