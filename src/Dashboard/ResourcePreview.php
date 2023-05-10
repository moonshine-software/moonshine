<?php

declare(strict_types=1);

namespace MoonShine\Dashboard;

use Closure;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;
use MoonShine\Resources\Resource;
use Throwable;

final class ResourcePreview extends DashboardItem
{
    protected static string $view = 'moonshine::blocks.resource_preview';

    /**
     * @deprecated Builder $query, use Closure $query
     */
    public function __construct(
        protected Resource $resource,
        string $label = '',
        protected Builder|Closure|null $query = null,
    ) {
        $this->setLabel($label);
    }

    public function resource(): Resource
    {
        return $this->resource
            ->previewMode();
    }

    /**
     * @throws Throwable
     */
    public function items(): Collection
    {
        $collections = $this->resource()
            ->resolveQuery()
            ->when($this->getQuery(), fn () => $this->getQuery())
            ->get();

        return $this->resource()
            ->transformToResources($collections);
    }

    protected function getQuery(): ?Builder
    {
        return is_callable($this->query)
            ? call_user_func($this->query)
            : $this->query;
    }

    public function id(string $index = null): string
    {
        return str($this->resource()->routeNameAlias())
            ->prepend('resource_preview_')
            ->slug('_');
    }

    public function name(string $index = null): string
    {
        return $this->id($index);
    }

    public function label(): string
    {
        return $this->label ?? $this->resource->title();
    }
}
