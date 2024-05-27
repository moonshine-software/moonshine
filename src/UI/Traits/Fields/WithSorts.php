<?php

declare(strict_types=1);

namespace MoonShine\UI\Traits\Fields;

use Closure;
use Illuminate\Support\Arr;

trait WithSorts
{
    protected bool $sortable = false;

    protected Closure|string|null $sortableCallback = null;

    /**
     * Define whether if index page can be sorted by this field
     *
     * @return $this
     */
    public function sortable(Closure|string|null $callback = null): static
    {
        $this->sortable = true;
        $this->sortableCallback = $callback;

        return $this;
    }

    public function sortableCallback(): Closure|string|null
    {
        return $this->sortableCallback;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function sortQuery(?string $url = null): string
    {
        $sortData = [
            'sort' => ($this->sortActive() && $this->sortDirection('asc') ? '-' : '') . $this->getColumn(),
            'page' => moonshine()->getRequest('page', 1),
        ];

        if (is_null($url)) {
            return request()->fullUrlWithQuery($sortData);
        }

        $urlParse = parse_url($url);

        $separator = empty($urlParse['query']) ? '?' : '&';

        return $url . $separator . Arr::query($sortData);
    }

    public function sortActive(): bool
    {
        return $this->getSortColumnFromRequest() === $this->getColumn();
    }

    public function sortDirection(string $type): bool
    {
        return $this->getSortDirectionFromRequest() === strtolower($type);
    }

    protected function getSortColumnFromRequest(): ?string
    {
        if (($sort = moonshine()->getRequest('sort')) && is_string($sort)) {
            return ltrim($sort, '-');
        }

        return null;
    }

    protected function getSortDirectionFromRequest(): ?string
    {
        if (($sort = moonshine()->getRequest('sort')) && is_string($sort)) {
            return str_starts_with($sort, '-') ? 'desc' : 'asc';
        }

        return null;
    }
}
