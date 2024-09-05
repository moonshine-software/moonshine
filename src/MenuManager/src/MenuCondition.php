<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use MoonShine\Contracts\MenuManager\MenuElementContract;

final readonly class MenuCondition
{
    public function __construct(
        private iterable|MenuElementContract|Closure $data,
        private ?Closure $before = null,
        private ?Closure $after = null,
    ) {
    }

    public function getData(): iterable
    {
        $data = value($this->data);

        return is_iterable($data) ? $data : [$data];
    }

    public function hasBefore(): bool
    {
        return ! is_null($this->before);
    }

    public function isBefore(MenuElementContract $element): bool
    {
        return (bool) value($this->before, $element);
    }

    public function hasAfter(): bool
    {
        return ! is_null($this->after);
    }

    public function isAfter(MenuElementContract $element): bool
    {
        return (bool) value($this->after, $element);
    }
}
