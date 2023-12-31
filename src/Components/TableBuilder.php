<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\Contracts\Table\TableContract;
use MoonShine\Fields\Fields;
use MoonShine\Table\TableRow;
use MoonShine\Traits\HasAsync;
use MoonShine\Traits\Table\TableStates;
use Throwable;

/**
 * @method static static make(Fields|array $fields = [], Paginator|iterable $items = [], ?Paginator $paginator = null)
 */
final class TableBuilder extends IterableComponent implements TableContract
{
    use TableStates;
    use HasAsync;

    protected string $view = 'moonshine::components.table.builder';

    protected array $rows = [];

    protected ?Closure $trAttributes = null;

    protected ?Closure $systemTrAttributes = null;

    protected ?Closure $tdAttributes = null;

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

        $this->withAttributes([]);
    }

    /**
     * @throws Throwable
     */
    public function rows(): Collection
    {
        $tableFields = $this->getFields();

        return $this->getItems()->filter()->map(function (mixed $data, int $index) use ($tableFields): TableRow {
            $casted = $this->castData($data);
            $raw = $this->unCastData($data);

            $fields = $this->getFilledFields($raw, $casted, $index, $tableFields);

            return TableRow::make(
                $casted,
                $fields->values(),
                $this->getButtons($casted),
                $this->trAttributes,
                $this->tdAttributes,
                $this->systemTrAttributes
            );
        });
    }

    public function trAttributes(Closure $closure): self
    {
        $this->trAttributes = $closure;

        return $this;
    }

    protected function systemTrAttributes(Closure $closure): self
    {
        $this->systemTrAttributes = $closure;

        return $this;
    }

    public function tdAttributes(Closure $closure): self
    {
        $this->tdAttributes = $closure;

        return $this;
    }

    protected function prepareAsyncUrl(?string $asyncUrl = null): ?string
    {
        return $asyncUrl ?? moonshineRouter()->asyncTable($this->getName());
    }

    protected function viewData(): array
    {
        if ($this->isAsync() && $this->hasPaginator()) {
            $this->getPaginator()
                ?->appends(request()->except('page'))
                ?->setPath($this->prepareAsyncUrlFromPaginator());
        }

        if (! is_null($this->sortableUrl) && $this->isSortable()) {
            $this->customAttributes([
                'data-sortable-url' => $this->sortableUrl,
                'data-sortable-group' => $this->sortableGroup,
            ])->systemTrAttributes(
                fn (mixed $data, int $index, ComponentAttributeBag $attr)
                    => $attr->merge(['data-id' => data_get($data, $this->sortableKey ?? 'id', $index)])
            );
        }

        return [
                'rows' => $this->rows(),
                'fields' => $this->getFields(),
                'name' => $this->getName(),
                'hasPaginator' => $this->hasPaginator(),
                'simple' => $this->isSimple(),
                'simplePaginate' => ! $this->getPaginator() instanceof LengthAwarePaginator,
                'paginator' => $this->getPaginator(),
                'bulkButtons' => $this->getBulkButtons(),
                'async' => $this->isAsync(),
                'asyncUrl' => $this->asyncUrl(),
                'createButton' => $this->creatableButton,
            ] + $this->statesToArray();
    }
}
