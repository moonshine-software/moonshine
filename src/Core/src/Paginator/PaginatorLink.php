<?php

declare(strict_types=1);

namespace MoonShine\Core\Paginator;

use MoonShine\Contracts\Core\Paginator\PaginatorLinkContract;

final readonly class PaginatorLink implements PaginatorLinkContract
{
    public function __construct(
        private string $url,
        private string $label,
        private bool $active = false,
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return array<string, string|bool>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->getUrl(),
            'label' => $this->getLabel(),
            'active' => $this->isActive(),
        ];
    }

    /**
     * @return array<string, string|bool>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
