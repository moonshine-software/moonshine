<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Illuminate\View\ComponentAttributeBag;

/**
 * @mixin ComponentAttributeBag
 */
interface ComponentAttributesBagContract
{
    public function concat(string $name, string $value, string $separator = ' '): void;

    public function set(string $name, string|bool $value): void;

    public function remove(string $name): void;
}
