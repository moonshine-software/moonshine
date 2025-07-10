<?php

declare(strict_types=1);

namespace MoonShine\MenuManager;

use Closure;
use MoonShine\Contracts\MenuManager\MenuElementContract;

final readonly class MenuCondition
{
    /**
     * @param  iterable<array-key, MenuElementContract>|MenuElementContract|(Closure(): iterable<array-key, MenuElementContract>|MenuElementContract)  $data
     * @param  (Closure(MenuElementContract):bool)|null  $before
     * @param  (Closure(MenuElementContract):bool)|null  $after
     */
    public function __construct(
        private iterable|MenuElementContract|Closure $data,
        private ?Closure $before = null,
        private ?Closure $after = null,
    ) {
    }

    /**
     * @return iterable<array-key, MenuElementContract>
     */
    public function getData(): iterable
    {
        $data = $this->data instanceof Closure
            ? \call_user_func($this->data)
            : $this->data;

        if ($data instanceof MenuElementContract) {
            return [$data];
        }

        return $data;
    }

    public function hasBefore(): bool
    {
        return ! \is_null($this->before);
    }

    public function isBefore(MenuElementContract $element): bool
    {
        if (! $this->before instanceof Closure) {
            return false;
        }

        return \call_user_func($this->before, $element);
    }

    public function hasAfter(): bool
    {
        return ! \is_null($this->after);
    }

    public function isAfter(MenuElementContract $element): bool
    {
        if (! $this->after instanceof Closure) {
            return false;
        }

        return \call_user_func($this->after, $element);
    }
}
