<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;

trait WithLink
{
    protected bool $isLink = false;

    protected string|Closure $linkValue = '';

    protected string|Closure $linkName = '';

    protected ?string $linkIcon = null;

    protected bool $linkBlank = false;

    protected bool $withoutIcon = false;

    public function hasLink(): bool
    {
        return $this->isLink;
    }

    public function getLinkValue(mixed $value = null): string|Closure
    {
        return value($this->linkValue, $value ?? $this->toValue(withDefault: false), $this);
    }

    public function getLinkName(mixed $value = null): string
    {
        return value($this->linkName, $value ?? $this->toValue(withDefault: false), $this);
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
