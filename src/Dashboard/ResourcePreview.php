<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Dashboard;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;
use Leeto\MoonShine\Contracts\HtmlViewable;
use Leeto\MoonShine\Resources\Resource;
use Leeto\MoonShine\Traits\Makeable;
use Leeto\MoonShine\Traits\WithView;

final class ResourcePreview implements HtmlViewable
{
    use Makeable;
    use WithView;

    public function __construct(
        protected Resource $resource,
        protected ?string $label = null,
        protected ?Builder $query = null,
    ) {
    }

    public function resource(): Resource
    {
        return $this->resource
            ->previewMode();
    }

    public function items(): Collection
    {
        return $this->resource()
            ->query()
            ->when($this->query, fn () => $this->query)
            ->get();
    }

    public function id(string $index = null): string
    {
        return 'resource-preview-' . $this->resource()->routeAlias();
    }

    public function name(string $index = null): string
    {
        return $this->id($index);
    }

    public function label(): string
    {
        return $this->label ?? $this->resource->title();
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getView(): string
    {
        return 'moonshine::blocks.resource_preview';
    }
}
