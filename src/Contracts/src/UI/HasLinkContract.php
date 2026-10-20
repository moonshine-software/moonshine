<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;

interface HasLinkContract
{
    public function hasLink(): bool;

    public function getLinkValue(mixed $value = null): string|Closure;

    public function getLinkName(mixed $value = null): string;

    public function getLinkIcon(): ?string;

    public function isLinkBlank(): bool;

    public function isWithoutIcon(): bool;

    /**
     * @param  string|(Closure(string $value, static $ctx): string)  $link
     * @param  string|(Closure(string $value, static $ctx): string)  $name
     */
    public function link(
        string|Closure $link,
        string|Closure $name = '',
        ?string $icon = null,
        bool $withoutIcon = false,
        bool $blank = false,
    ): static;
}
