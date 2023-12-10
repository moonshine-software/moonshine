<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use MoonShine\Components\IterableComponent;
use Illuminate\Contracts\Pagination\Paginator;
use MoonShine\Fields\Fields;
use MoonShine\Traits\WithColumnSpan;
use Illuminate\Support\Collection;
use MoonShine\Traits\HasAsync;

/**
 * @method static static make(Fields|array $fields = [], Paginator|iterable $items = [], ?Paginator $paginator = null)
 */
final class CardBuilder extends IterableComponent
{
    use WithColumnSpan;
    use HasAsync;

    protected string $view = 'moonshine::components.card.builder';

    protected bool $overlay = false;

    protected ?Closure $actions = null;

    protected iterable $items = [];

    public function __construct(
        Fields|array $fields = [],
        Paginator|iterable $items = [],
        ?Paginator $paginator = null
    ) {
        $this->fields($fields);
        $this->items($items);

        if (! is_null($paginator)) {
            $this->paginator($paginator);
        }

    }

    public function getItems(): Collection
    {
        return collect($this->items);
    }

    /**
     * @throws Throwable
     */
    protected function getFilledFields(array $raw = [], mixed $casted = null, int $index = 0): Fields
    {
        $fields = $this->getFields();

        if (is_closure($this->fieldsClosure)) {
            $fields->fill($raw, $casted, $index);

            return $fields;
        }

        return $fields->fillCloned($raw, $casted, $index);
    }

    public function rows(): Collection
    {
        return $this->getItems()->filter()->map(function (mixed $data, $index): Fields {
            $casted = $this->castData($data);
            $raw = $this->unCastData($data);

            return $this->getFilledFields($raw, $casted, $index);
        });
    }
    protected function viewData(): array
    {
        if ($this->isAsync() && $this->hasPaginator()) {
            $this->getPaginator()
                ?->setPath($this->prepareAsyncUrlFromPaginator());
        }
        return [
            'rows' => $this->rows(),
            'colSpan' => $this->columnSpanValue(),
            'adaptivecolSpan' => $this->adaptiveColumnSpanValue(),
            'overlay' => $this->isOverlay()
        ];
    }

    public function overlay(): static
    {
        $this->overlay = true;

        return $this;
    }

    public function isOverlay(): bool
    {
        return $this->overlay;
    }
}
