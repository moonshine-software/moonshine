<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Dashboard;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Resources\Resource;

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

    public function items(): Collection
    {
        return $this->resource()
            ->resolveQuery()
            ->when($this->query, fn () => $this->query)
            ->get();
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
