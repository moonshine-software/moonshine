<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use Illuminate\Support\Arr;

trait WithSorts
{
    protected bool $sortable = false;

    protected string $sortablePrefix = '';

    protected Closure|string|null $sortableCallback = null;

    /**
     * Define whether if index page can be sorted by this field
     */
    public function sortable(Closure|string|null $callback = null): static
    {
        $this->sortable = true;
        $this->sortableCallback = $callback;

        return $this;
    }

    public function sortablePrefix(string $prefix): static
    {
        $this->sortablePrefix = $prefix;

        return $this;
    }

    public function disableSortable(): static
    {
        $this->sortable = false;

        return $this;
    }

    public function getSortableCallback(): Closure|string|null
    {
        return $this->sortableCallback;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function getSortQuery(?string $url = null): string
    {
        $sortData = [
            $this->sortablePrefix . 'sort' => ($this->isSortActive() && $this->sortDirectionIs('asc') ? '-' : '') . $this->getColumn(),
            $this->sortablePrefix . 'page' => $this->getCore()->getRequest()->getScalar($this->sortablePrefix . 'page', 1),
        ];

        if (\is_null($url)) {
            return $this->getCore()->getRequest()->getUrlWithQuery($sortData);
        }

        $parsed = parse_url($url);
        parse_str($parsed['query'] ?? '', $query);

        $params = array_merge($query, $sortData);

        return strtok($url, '?') . '?' . Arr::query($params);
    }

    public function isSortActive(): bool
    {
        return $this->getSortColumnFromRequest() === $this->getColumn();
    }

    public function sortDirectionIs(string $type): bool
    {
        return $this->getSortDirectionFromRequest() === strtolower($type);
    }

    protected function getSortColumnFromRequest(): ?string
    {
        if ($sort = $this->getCore()->getRequest()->getScalar($this->sortablePrefix . 'sort')) {
            return ltrim((string) $sort, '-');
        }

        return null;
    }

    protected function getSortDirectionFromRequest(): ?string
    {
        if ($sort = $this->getCore()->getRequest()->getScalar($this->sortablePrefix . 'sort')) {
            return str_starts_with((string) $sort, '-') ? 'desc' : 'asc';
        }

        return null;
    }
}
