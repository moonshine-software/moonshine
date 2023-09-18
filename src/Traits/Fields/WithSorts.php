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
            'sort' => [
                'column' => $this->column(),
                'direction' => $this->sortActive() && $this->sortDirection('asc') ? 'desc'
                    : 'asc',
            ],
            'page' => request('page', 1),
        ];

        if (is_null($url)) {
            return request()->fullUrlWithQuery($sortData);
        }

        return $url . '?' . Arr::query($sortData);
    }

    public function sortActive(): bool
    {
        return request('sort.column') === $this->column();
    }

    public function sortDirection(string $type): bool
    {
        return request('sort.direction') === strtolower($type);
    }
}
