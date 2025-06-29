<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Core\DependencyInjection;

use Countable;

interface TranslatorContract
{
    /**
     * @return array<string, mixed>
     */
    public function all(?string $locale = null): array;

    /**
     * @param  array<string, string|int|float>  $replace
     */
    public function get(string $key, array $replace = [], ?string $locale = null): mixed;

    /**
     * @param  int[]|Countable|float|int  $number
     * @param  array<string, string|int|float>  $replace
     */
    public function choice(string $key, array|Countable|float|int $number, array $replace = [], ?string $locale = null): string;

    public function getLocale(): string;
}
