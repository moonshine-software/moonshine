<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Contracts\Table\TableContract;
use MoonShine\Fields\Field;
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

    protected $except = [
        'rows',
        'fields',
        'hasPaginator',
        'paginator',
    ];

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

    public function getItems(): Collection
    {
        return collect($this->items);
    }

    public function rows(): Collection
    {
        return $this->getItems()->filter()->map(function (mixed $data, $index): TableRow {
            $casted = $this->castData($data);
            $raw = $this->unCastData($data);

            $fields = $this->getFilledFields($raw, $casted, $index);

            if (! is_null($this->getName())) {
                $fields->onlyFields()->each(
                    fn (Field $field): Field => $field->formName($this->getName())
                );
            }

            return TableRow::make(
                $casted,
                $fields,
                $this->getButtons($casted),
                $this->trAttributes,
                $this->tdAttributes,
                $this->systemTrAttributes
            );
        });
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

    public function getBulkButtons(): ActionButtons
    {
        return ActionButtons::make($this->buttons)
            ->bulk()
            ->onlyVisible();
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
        return $asyncUrl ?? tableAsyncRoute($this->getName());
    }

    protected function prepareAsyncUrlFromPaginator(): string
    {
        $withoutQuery = strtok($this->asyncUrl(), '?');

        if (! $withoutQuery) {
            return $this->asyncUrl();
        }

        $query = parse_url($this->asyncUrl(), PHP_URL_QUERY);

        parse_str($query, $asyncUri);

        $paginatorUri = $this->getPaginator()
            ->resolveQueryString();

        $asyncUri = array_filter(
            $asyncUri,
            static fn ($value, $key): bool => ! isset($paginatorUri[$key]),
            ARRAY_FILTER_USE_BOTH
        );

        if ($asyncUri !== []) {
            return $withoutQuery . "?" . Arr::query($asyncUri);
        }

        return $withoutQuery;
    }

    protected function viewData(): array
    {
        if ($this->isAsync() && $this->hasPaginator()) {
            $this->getPaginator()
                ?->setPath($this->prepareAsyncUrlFromPaginator());
        }

        if (! is_null($this->creatableLimit) && $this->isCreatable()) {
            $this->customAttributes([
                'data-creatable-limit' => $this->creatableLimit,
            ]);
        }

        if (! is_null($this->sortableUrl) && $this->isSortable()) {
            $this->customAttributes([
                'data-sortable-url' => $this->sortableUrl,
                'data-sortable-group' => $this->sortableGroup,
            ])->systemTrAttributes(
                fn(mixed $data, int $index, ComponentAttributeBag $attr)
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
            ] + $this->statesToArray();
    }
}
