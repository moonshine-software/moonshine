<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Renderable;
use MoonShine\Resources\Resource;
use MoonShine\Traits\Makeable;
use MoonShine\Traits\WithLabel;
use MoonShine\Traits\WithUniqueId;
use Throwable;

/**
 * @method static static make(Resource $resource, string $label = '', Builder|Closure|null $query = null)
 */
final class ResourcePreview extends Component implements Renderable
{
    use Makeable;
    use WithUniqueId;
    use WithLabel;

    public function __construct(
        protected Resource $resource,
        string $label = '',
        protected ?Closure $query = null,
    ) {
        $this->setLabel($label);
    }

    /**
     * @throws Throwable
     */
    public function items(): Collection
    {
        $collections = $this->resource()
            ->resolveQuery()
            ->when($this->getQuery(), fn (): ?Builder => $this->getQuery())
            ->get();

        return $this->resource()
            ->transformToResources($collections);
    }

    public function resource(): Resource
    {
        return $this->resource
            ->previewMode();
    }

    protected function getQuery(): ?Builder
    {
        return is_callable($this->query)
            ? call_user_func($this->query)
            : $this->query;
    }

    public function id(string $index = null): string
    {
        return str($this->resource()->uriKey())
            ->prepend('resource_preview_')
            ->slug('_');
    }

    public function label(): string
    {
        return $this->label ?? $this->resource->title();
    }

    public function render(): View|Closure|string
    {
        return view('moonshine::components.resource-preview', [
            'element' => $this
        ]);
    }

    public function __toString()
    {
        return (string) $this->render();
    }
}
