<?php

declare(strict_types=1);

namespace MoonShine\Dashboard;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;
use MoonShine\Resources\Resource;
use Throwable;

final class ResourcePreview extends DashboardItem
{
    protected static string $view = 'moonshine::blocks.resource_preview';

    public function __construct(
        protected Resource $resource,
        string $label = '',
        protected ?Builder $query = null,
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
            ->when($this->query, fn () => $this->query)
            ->get();

        return $this->resource()
            ->transformToResources($collections);
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
