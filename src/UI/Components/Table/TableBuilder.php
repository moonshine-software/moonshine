<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Table;

use Closure;
use MoonShine\Core\Contracts\CastedData;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Traits\HasAsync;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Collections\TableCells;
use MoonShine\UI\Collections\TableRows;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\IterableComponent;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Contracts\Collections\FieldsCollection;
use MoonShine\UI\Contracts\Table\TableContract;
use MoonShine\UI\Fields\Checkbox;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Td;
use MoonShine\UI\Traits\Table\TableStates;
use Throwable;

/**
 * @method static static make(FieldsCollection|array $fields = [], iterable $items = [])
 */
final class TableBuilder extends IterableComponent implements TableContract
{
    use TableStates;
    use HasAsync;

    protected string $view = 'moonshine::components.table.builder';

    protected Closure|TableRows|null $rows = null;

    protected Closure|TableRows|null $headRows = null;

    protected Closure|TableRows|null $footRows = null;

    protected array $trAttributes = [];

    protected array $tdAttributes = [];

    protected MoonShineComponentAttributeBag $headAttributes;

    protected MoonShineComponentAttributeBag $bodyAttributes;

    protected MoonShineComponentAttributeBag $footAttributes;

    public function __construct(
        FieldsCollection|array $fields = [],
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

    public function getPreparedFields(): FieldsCollection
    {
        return memoize(function () {
            $fields = $this->getFields();

            if (! $this->isEditable()) {
                $fields = $fields
                    ->onlyFields(withWrappers: true)
                    ->map(
                        static fn (Field $field): Field => $field
                            ->withoutWrapper()
                            ->previewMode()
                    );
            }

            return $fields->values();
        });
    }

    /**
     * @param  Closure(mixed $data, int $row, self $table): array  $closure
     */
    public function trAttributes(Closure $closure): self
    {
        $this->trAttributes[] = $closure;

        return $this;
    }

    public function getTrAttributes(mixed $data, int $row): array
    {
        return collect($this->trAttributes)
            ->flatMap(fn (Closure $callback) => value($callback, $data, $row, $this))
            ->toArray();
    }

    /**
     * @param  Closure(mixed $data, int $row, int $cell, self $table): array  $closure
     */
    public function tdAttributes(Closure $closure): self
    {
        $this->tdAttributes[] = $closure;

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
        return $url ?? fn (): string => moonshineRouter()->getEndpoints()->asyncComponent(
            $this->getName(),
            additionally: [
                'filters' => moonshine()->getRequest()->get('filters'),
                'query-tag' => moonshine()->getRequest()->get('query-tag'),
                'search' => moonshine()->getRequest()->get('search'),
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
     * @param  TableRows|Closure(TableRow $default): TableRows  $rows
     */
    public function rows(TableRows|Closure $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function getRows(): TableRows
    {
        if ($this->rows instanceof TableRows) {
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
    private function resolveRows(): TableRows
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

        foreach ($this->getItems() as $index => $data) {
            $casted = $this->castData($data);
            $cells = TableCells::make();

            $fields = $this
                ->getFilledFields($casted->toArray(), $casted, $index, $tableFields)
                ->when(
                    $this->isReindex() && ! $this->isPreparedReindex(),
                    static fn (Fields $f): Fields => $f->prepareReindex()
                );

            $key = $casted->getKey();

            if ($this->isVertical()) {
                foreach ($fields as $cellIndex => $field) {
                    $builder = null;

                    if($field instanceof Td && $field->hasTdAttributes()) {
                        $builder = static fn (TableTd $td): TableTd => $td->customAttributes(
                            $field->resolveTdAttributes($field->getData())
                        );
                    }

                    $cells = TableCells::make()
                        ->pushCell($field->getLabel(), builder: static fn (TableTd $td): TableTd => $td->customAttributes([
                            'width' => '20%',
                            'class' => 'font-semibold',
                        ]))
                        ->pushCell((string) $field, builder: $builder);

                    $rows->pushRow($cells, $key ?? $cellIndex);
                }

                continue;
            }

            $buttons = $this->getButtons($casted);

            $cells
                ->pushCellWhen(
                    ! $this->isPreview() && $this->getBulkButtons()->isNotEmpty(),
                    fn (): string => (string) $this->getRowCheckbox($key)
                )
                ->pushFields(
                    $fields,
                    builder: fn (TableTd $td): TableTd => $td->customAttributes(
                        $this->getTdAttributes($casted, $index, $td->getIndex())
                    )
                )
                ->pushCellWhen(
                    $buttons->isNotEmpty(),
                    static fn (): string => (string) Flex::make([
                        ActionGroup::make($buttons->toArray()),
                    ])->justifyAlign('end')
                );

            $rows->pushRow(
                $cells,
                $key,
                builder: fn (TableRow $tr): TableRow => $tr->customAttributes($this->getTrAttributes($casted, $index))
            );
        }

        return $rows->when(
            $this->isVertical(),
            static fn (TableRows $rows) => $rows->flatten()
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
        return fn (?CastedData $data, int $index): array => is_null($data)
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
     * @param  TableRows|Closure(TableRow $default): TableRows  $rows
     */
    public function headRows(TableRows|Closure $rows): self
    {
        $this->headRows = $rows;

        return $this;
    }

    /**
     * @throws Throwable
     */
    protected function getHeadRows(): TableRows
    {
        if ($this->headRows instanceof TableRows) {
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
            $cells->pushWhen(
                ! $this->isPreview() && $this->getBulkButtons()->isNotEmpty(),
                fn () => TableTh::make(
                    (string) $this->getRowBulkCheckbox()
                )->class('w-10 text-center')
            );

            foreach ($this->getPreparedFields() as $field) {
                $cells->push(
                    $field->isSortable() && ! $this->isPreview()
                        ?
                        TableTh::make(
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
                        )
                        : TableTh::make($field->getLabel())
                );
            }

            $cells->pushWhen(
                $this->hasButtons(),
                static fn () => TableTh::make('')
            );
        }

        return TableRow::make($cells);
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
     * @param  TableRows|Closure(TableRow $default): TableRows  $rows
     */
    public function footRows(TableRows|Closure $rows): self
    {
        $this->footRows = $rows;

        return $this;
    }

    protected function getFootRows(): TableRows
    {
        if ($this->footRows instanceof TableRows) {
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

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'rows' => $this->getRows(),
            'headRows' => $this->getHeadRows(),
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
