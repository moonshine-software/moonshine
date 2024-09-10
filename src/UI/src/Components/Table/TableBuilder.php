<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Table;

use Closure;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Contracts\UI\TableRowsContract;
use MoonShine\Contracts\UI\WithoutExtractionContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Collections\TableCells;
use MoonShine\UI\Collections\TableRows;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\IterableComponent;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Fields\Checkbox;
use MoonShine\UI\Traits\HasAsync;
use MoonShine\UI\Traits\Table\TableStates;
use Throwable;

/**
 * @method static static make(iterable $fields = [], iterable $items = [])
 */
final class TableBuilder extends IterableComponent implements TableBuilderContract, WithoutExtractionContract
{
    use TableStates;
    use HasAsync;

    protected string $view = 'moonshine::components.table.builder';

    protected array $translates = [
        'search' => 'moonshine::ui.search',
        'notfound' => 'moonshine::ui.notfound',
    ];

    protected Closure|TableRowsContract|null $rows = null;

    protected Closure|TableRowsContract|null $headRows = null;

    protected Closure|TableRowsContract|null $footRows = null;

    protected array $trAttributes = [];

    protected array $tdAttributes = [];

    protected MoonShineComponentAttributeBag $headAttributes;

    protected MoonShineComponentAttributeBag $bodyAttributes;

    protected MoonShineComponentAttributeBag $footAttributes;

    public function __construct(
        iterable $fields = [],
        iterable $items = [],
    ) {
        parent::__construct();

        $this->fields($fields);
        $this->items($items);

        $this->withAttributes([]);

        $this->headAttributes = new MoonShineComponentAttributeBag();
        $this->bodyAttributes = new MoonShineComponentAttributeBag();
        $this->footAttributes = new MoonShineComponentAttributeBag();
    }

    protected function prepareFields(): FieldsContract
    {
        $fields = $this->getFields();

        if (! $this->isEditable()) {
            $fields = $fields
                ->onlyFields(withWrappers: true)
                ->map(
                    static fn (FieldContract $field): FieldContract => $field
                        ->withoutWrapper()
                        ->previewMode()
                );
        }

        return $fields->values();
    }

    /**
     * @param  Closure(mixed $data, int $row, self $table): array  $callback
     */
    public function trAttributes(Closure $callback): self
    {
        $this->trAttributes[] = $callback;

        return $this;
    }

    public function getTrAttributes(mixed $data, int $row): array
    {
        return collect($this->trAttributes)
            ->flatMap(fn (Closure $callback) => value($callback, $data, $row, $this))
            ->toArray();
    }

    /**
     * @param  Closure(mixed $data, int $row, int $cell, self $table): array  $callback
     */
    public function tdAttributes(Closure $callback): self
    {
        $this->tdAttributes[] = $callback;

        return $this;
    }

    public function getTdAttributes(mixed $data, int $row, int $cell): array
    {
        return collect($this->tdAttributes)
            ->flatMap(fn (Closure $callback) => value($callback, $data, $row, $cell, $this))
            ->toArray();
    }

    protected function prepareAsyncUrl(Closure|string|null $url = null): Closure|string|null
    {
        return $url ?? fn (): string => $this->getCore()->getRouter()->getEndpoints()->component(
            $this->getName(),
            additionally: [
                'filter' => $this->getCore()->getRequest()->get('filter'),
                'query-tag' => $this->getCore()->getRequest()->get('query-tag'),
                'search' => $this->getCore()->getRequest()->get('search'),
            ]
        );
    }

    public function headAttributes(array $attributes): self
    {
        $this->headAttributes = $this->headAttributes->merge($attributes);

        return $this;
    }

    public function bodyAttributes(array $attributes): self
    {
        $this->bodyAttributes = $this->bodyAttributes->merge($attributes);

        return $this;
    }

    public function footAttributes(array $attributes): self
    {
        $this->footAttributes = $this->footAttributes->merge($attributes);

        return $this;
    }

    /**
     * @param  TableRowsContract|Closure(TableRowsContract $default): TableRowsContract  $rows
     */
    public function rows(TableRowsContract|Closure $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function getRows(): TableRowsContract
    {
        if ($this->rows instanceof TableRowsContract) {
            return $this->rows;
        }

        if (! is_null($this->rows)) {
            return $this->rows = value($this->rows, $this->resolveRows(), $this);
        }

        return $this->rows = $this->resolveRows();
    }

    /**
     * @throws Throwable
     */
    private function resolveRows(): TableRowsContract
    {
        $tableFields = $this->getPreparedFields();

        $rows = TableRows::make();

        if ($this->isAsync()) {
            $this->trAttributes(
                $this->getRowAsyncAttributes()
            );
        }

        if (! is_null($this->reorderableUrl) && $this->isReorderable()) {
            $this->trAttributes(
                $this->getRowReorderAttributes()
            );
        }

        $index = 0;
        foreach ($this->getItems() as $data) {
            $casted = $this->castData($data);
            $cells = TableCells::make();

            $fields = $this
                ->getFilledFields($casted->toArray(), $casted, $index, $tableFields)
                ->onlyVisible()
                ->when(
                    $this->isReindex() && ! $this->isPreparedReindex(),
                    static fn (FieldsContract $f): FieldsContract => $f->prepareReindexNames()
                );

            $key = $casted->getKey();

            $tdAttributes = fn (TableTd $td): TableTd => $td->customAttributes(
                $this->getTdAttributes($casted, $index + 1, $td->getIndex())
            );

            $trAttributes = fn (TableRow $tr): TableRow => $tr->customAttributes(
                $this->getTrAttributes($casted, $index + ($this->isVertical() ? 0 : 1))
            );

            if ($this->isVertical()) {
                foreach ($fields as $cellIndex => $field) {
                    $attributes = $field->getWrapperAttributes()->jsonSerialize();

                    $builder = $attributes !== [] ? static fn (TableTd $td): TableTd => $td->customAttributes(
                        $field->getWrapperAttributes()->jsonSerialize()
                    ) : null;

                    $cells = TableCells::make()
                        ->pushCell(
                            $field->getLabel(),
                            builder: static fn (TableTd $td): TableTd => $td->customAttributes([
                                'width' => '20%',
                                'class' => 'font-semibold',
                            ])
                        )
                        ->pushCell((string) $field, builder: $builder);

                    $rows->pushRow($cells, $key ?? $cellIndex);
                }

                $index++;
                continue;
            }

            $buttons = $this->getButtons($casted);
            $hasBulk = ! $this->isPreview() && $this->getBulkButtons()->isNotEmpty();

            $cells
                ->pushCellWhen(
                    $hasBulk,
                    fn (): string => (string) $this->getRowCheckbox($key),
                    builder: $tdAttributes
                )
                ->pushFields(
                    $fields,
                    builder: $tdAttributes,
                    startIndex: $hasBulk ? 1 : 0
                )
                ->pushCell(
                    static fn (): string => (string) Flex::make([
                        ActionGroup::make($buttons->toArray()),
                    ])->justifyAlign('end'),
                    index: $fields->count() + ($hasBulk ? 1 : 0),
                    builder: $tdAttributes
                );

            $rows->pushRow(
                $cells,
                $key,
                builder: $trAttributes
            );

            $index++;
        }

        return $rows->when(
            $this->isVertical(),
            static fn (TableRowsContract $rows) => $rows->flatten()
        );
    }

    public function getRowCheckbox(int|string|null $key): Checkbox
    {
        return Checkbox::make('')
            ->setValue($key)
            ->setNameAttribute("items[$key]")
            ->withoutWrapper()
            ->simpleMode()
            ->customAttributes([
                'autocomplete' => 'off',
                '@change' => "actions('row', \$id('table-component'))",
                ':class' => "\$id('table-component') + '-table-action-row'",
                'class' => 'js-table-action-row',
            ]);
    }

    public function getRowAsyncAttributes(): Closure
    {
        return fn (?DataWrapperContract $data, int $index): array => is_null($data)
            ? []
            : [
                AlpineJs::eventBlade(
                    JsEvent::TABLE_ROW_UPDATED,
                    "{$this->getName()}-{$data->getKey()}",
                ) => "asyncRowRequest(`{$data->getKey()}`,`$index`)",
            ];
    }

    public function getRowReorderAttributes(): Closure
    {
        return fn (mixed $data, int $index): array => [
            'data-id' => data_get($data, $this->reorderableKey ?? 'id', $index),
        ];
    }

    /**
     * @param  TableRowsContract|Closure(TableRowsContract $default): TableRowsContract  $rows
     */
    public function headRows(TableRowsContract|Closure $rows): self
    {
        $this->headRows = $rows;

        return $this;
    }

    /**
     * @throws Throwable
     */
    protected function getHeadRows(): TableRowsContract
    {
        if ($this->headRows instanceof TableRowsContract) {
            return $this->headRows;
        }

        if (! is_null($this->headRows)) {
            return $this->headRows = value($this->headRows, $this->resolveHeadRow(), $this);
        }

        return $this->headRows = TableRows::make([
            $this->resolveHeadRow(),
        ]);
    }

    /**
     * @throws Throwable
     */
    private function resolveHeadRow(): TableRow
    {
        $cells = TableCells::make();

        if (! $this->isVertical()) {
            $hasBulk = ! $this->isPreview() && $this->getBulkButtons()->isNotEmpty();
            $index = $hasBulk ? 1 : 0;
            $tdAttributes = fn ($i): array => $this->getTdAttributes(null, 0, $i);

            $cells->pushWhen(
                $hasBulk,
                fn () => TableTh::make(
                    (string) $this->getRowBulkCheckbox()
                )
                    ->customAttributes($tdAttributes(0))
                    ->class('w-10 text-center')
            );

            foreach ($this->getPreparedFields()->onlyVisible() as $field) {
                $thContent = $field->isSortable() && ! $this->isPreview()
                    ?
                    (string) Link::make(
                        $field->getSortQuery($this->getAsyncUrl()),
                        $field->getLabel()
                    )
                        ->icon(
                            $field->isSortActive() && $field->sortDirectionIs('desc') ? 'bars-arrow-down'
                                : 'bars-arrow-up'
                        )
                        ->customAttributes([
                            '@click.prevent' => $this->isAsync() ? 'asyncRequest' : null,
                        ])
                    : $field->getLabel();

                $cells->push(
                    TableTh::make($thContent)
                        ->customAttributes(['data-column-selection' => $field->getIdentity()])
                        ->customAttributes($tdAttributes($index))
                );

                $index++;
            }

            $cells->pushWhen(
                $this->hasButtons(),
                static fn () => TableTh::make('')->customAttributes($tdAttributes($index))
            );
        }

        return TableRow::make($cells)
            ->customAttributes($this->getTrAttributes(null, 0));
    }

    public function getRowBulkCheckbox(): Checkbox
    {
        return Checkbox::make('')
            ->withoutWrapper()
            ->simpleMode()
            ->customAttributes([
                'autocomplete' => 'off',
                '@change' => "actions('all', \$id('table-component'))",
                ':class' => "\$id('table-component') + '-actions-all-checked'",
            ])
            ->class('js-actions-all-checked');
    }

    /**
     * @param  TableRowsContract|Closure(TableRowsContract $default): TableRowsContract  $rows
     */
    public function footRows(TableRowsContract|Closure $rows): self
    {
        $this->footRows = $rows;

        return $this;
    }

    protected function getFootRows(): TableRowsContract
    {
        if ($this->footRows instanceof TableRowsContract) {
            return $this->footRows;
        }

        if (! is_null($this->footRows)) {
            return $this->footRows = value($this->footRows, $this->resolveFootRow(), $this);
        }

        return $this->footRows = TableRows::make([
            $this->resolveFootRow(),
        ]);
    }

    private function resolveFootRow(): TableRow
    {
        $cells = TableCells::make()->pushCellWhen(
            ! $this->isPreview(),
            fn (): string => (string) Flex::make([
                ActionGroup::make($this->getBulkButtons()->toArray()),
            ])->justifyAlign('start'),
            builder: static fn (TableTd $td): TableTd => $td->customAttributes([
                'colspan' => 6,
                ':class' => "\$id('table-component') + '-bulk-actions'",
            ])
        );

        if ($this->getBulkButtons()->isNotEmpty()) {
            $this->footAttributes([
                ':class' => "actionsOpen ? 'translate-y-none ease-out' : '-translate-y-full ease-in hidden'",
            ]);
        }

        return TableRow::make($cells);
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->performBeforeRender();
    }

    protected function performBeforeRender(): self
    {
        $this->resolvePaginator();

        if ($this->isAsync() && $this->hasPaginator()) {
            $this->paginator(
                $this->getPaginator()?->setPath($this->prepareAsyncUrlFromPaginator())
            );
        }

        if ($this->isAsync()) {
            $this->customAttributes([
                'data-events' => $this->getAsyncEvents(),
            ]);
        }

        if (! is_null($this->reorderableUrl) && $this->isReorderable()) {
            $this->customAttributes([
                'data-sortable-url' => $this->reorderableUrl,
                'data-sortable-group' => $this->reorderableGroup,
            ]);
        }

        if ($this->isCreatable() && ! $this->isPreview()) {
            $this->items(
                $this->getItems()->push([null])
            );
        }

        return $this;
    }

    public function inside(string $entity): self
    {
        return $this->customAttributes([
            'data-inside' => $entity,
        ]);
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        $columns = $this->getFields()->onlyVisible()->flatMap(
            static fn (FieldContract $field): ?array => $field->isColumnSelection()
                ? [$field->getIdentity() => $field->getLabel()]
                : null
        )->filter()->toArray();

        return [
            'rows' => $this->getRows(),
            'headRows' => $this->getHeadRows(),
            'columns' => $columns,
            'footRows' => $this->getFootRows(),
            'name' => $this->getName(),
            'hasPaginator' => $this->hasPaginator(),
            'simple' => $this->isSimple(),
            'paginator' => $this->getPaginator(
                $this->isAsync()
            ),
            'async' => $this->isAsync(),
            'asyncUrl' => $this->getAsyncUrl(),
            'createButton' => $this->creatableButton,
            'headAttributes' => $this->headAttributes,
            'bodyAttributes' => $this->bodyAttributes,
            'footAttributes' => $this->footAttributes,
            ...$this->statesToArray(),
        ];
    }
}
