<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;

trait WithLink
{
    protected bool $isLink = false;

    /** @var string|(Closure(mixed, static): string) */
    protected string|Closure $linkValue = '';

    /** @var string|(Closure(mixed, static): string) */
    protected string|Closure $linkName = '';

    protected ?string $linkIcon = null;

    protected bool $linkBlank = false;

    protected bool $withoutIcon = false;

    public function hasLink(): bool
    {
        return $this->isLink;
    }

    public function getLinkValue(mixed $value = null): string
    {
        return $this->linkValue instanceof Closure
            ? ($this->linkValue)($value ?? $this->toValue(withDefault: false), $this)
            : $this->linkValue;
    }

    public function getLinkName(mixed $value = null): string
    {
        return $this->linkName instanceof Closure
            ? ($this->linkName)($value ?? $this->toValue(withDefault: false), $this)
            : $this->linkName;
    }

    public function getLinkIcon(): ?string
    {
        return $this->linkIcon;
    }

    public function isLinkBlank(): bool
    {
        return $this->linkBlank;
    }

    public function isWithoutIcon(): bool
    {
        return $this->withoutIcon;
    }

    /**
     * @param  string|(Closure(mixed $value, static $ctx): string)  $link
     * @param  string|(Closure(mixed $value, static $ctx): string)  $name
     */
    public function link(
        string|Closure $link,
        string|Closure $name = '',
        ?string $icon = null,
        bool $withoutIcon = false,
        bool $blank = false,
    ): static {
        $this->isLink = true;

        $this->linkIcon = $icon;
        $this->withoutIcon = $withoutIcon;
        $this->linkValue = $link;
        $this->linkName = $name;
        $this->linkBlank = $blank;

        return $this;
    }
}
