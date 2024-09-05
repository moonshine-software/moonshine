<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use Countable;

interface TranslatorContract
{
    public function all(?string $locale = null): array;

    public function get(string $key, array $replace = [], ?string $locale = null): mixed;

    public function choice(string $key, array|Countable|float|int $number, array $replace = [], ?string $locale = null): string;

    public function getLocale(): string;
}
