<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Support\Arr;

trait WithSorts
{
    protected bool $sortable = false;

    /**
     * Define whether if index page can be sorted by this field
     *
     * @return $this
     */
    public function sortable(): static
    {
        $this->sortable = true;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function sortQuery(?string $url = null): string
    {
        $sortData = [
            'sort' => ($this->sortActive() && $this->sortDirection('asc') ? '-' : '') . $this->column(),
            'page' => request('page', 1),
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
        return $this->getSortColumnFromRequest() === $this->column();
    }

    public function sortDirection(string $type): bool
    {
        return $this->getSortDirectionFromRequest() === strtolower($type);
    }

    private function getSortColumnFromRequest(): ?string
    {
        if (($sort = request('sort')) && is_string($sort)) {
            return ltrim($sort, '-');
        }

        return null;
    }

    private function getSortDirectionFromRequest(): ?string
    {
        if (($sort = request('sort')) && is_string($sort)) {
            return str_starts_with($sort, '-') ? 'desc' : 'asc';
        }

        return null;
    }
}
