<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Table;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\Collection\ActionButtonsContract;
use MoonShine\Contracts\UI\Collection\TableRowsContract;
use MoonShine\Contracts\UI\ComponentAttributesBagContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\HasFieldsContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Contracts\UI\TableCellContract;
use MoonShine\Contracts\UI\TableRowContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\EventParams\ListRowEventParams;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Collections\TableCells;
use MoonShine\UI\Collections\TableRows;
use MoonShine\UI\Components\ActionButton;
use MoonShine\UI\Components\ActionGroup;
use MoonShine\UI\Components\Components;
use MoonShine\UI\Components\Dropdown;
use MoonShine\UI\Components\FlexibleRender;
use MoonShine\UI\Components\IterableComponent;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Div;
use MoonShine\UI\Components\Layout\Flex;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Link;
use MoonShine\UI\Fields\Checkbox;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Traits\HasAsync;
use MoonShine\UI\Traits\Table\TableStates;
use MoonShine\UI\Traits\WithFields;
use Throwable;

/**
 * @template TData of mixed = mixed
 * @template TCaster of DataCasterContract<TData> = DataCasterContract
 * @template TWrapper of DataWrapperContract<TData> = DataWrapperContract
 *
 * @method static static make(iterable $fields = [], iterable $items = [])
 *
 * @implements HasFieldsContract<Fields|FieldsContract>
 * @implements TableBuilderContract<TData>
 * @extends  IterableComponent<TData, TCaster, TWrapper>
 */
final class TableBuilder extends IterableComponent implements
    TableBuilderContract,
    HasFieldsContract
{
    use WithFields;
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

    /**
     * @var  array<array-key, Closure>
     *
     */
    protected array $trAttributes = [];

    /**
     * @var  array<array-key, Closure>
     *
     */
    protected array $tdAttributes = [];

    protected ComponentAttributesBagContract $headAttributes;

    protected ComponentAttributesBagContract $bodyAttributes;

    protected ComponentAttributesBagContract $footAttributes;

    protected ?Closure $modifyRowCheckbox = null;

    protected ?Closure $topLeft = null;

    protected ?Closure $topRight = null;

    protected bool $isWithoutKey = false;

    /**
     * @param  iterable<array-key, FieldContract>  $fields
     * @param  iterable<array-key, TData>  $items
     */
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

    /**
     * @throws Throwable
     */
    protected function prepareFields(): FieldsContract
    {
        $fields = $this->getFields();

        if (! $this->isEditable()) {
            $fields = $fields
                ->onlyFields(withWrappers: true)
                ->map(
                    static fn (FieldContract $field): FieldContract => $field
                        ->withoutWrapper()
                        ->previewMode(),
                );
        }

        return $fields->values();
    }

    /**
     * @param  Closure(?DataWrapperContract $data, int $row, static $table): array<string, mixed>  $callback
     */
    public function trAttributes(Closure $callback): static
    {
        $this->trAttributes[] = $callback;

        return $this;
    }

    /**
     * @param null|DataWrapperContract<TData> $data
     * @return  array<string, mixed>
     */
    public function getTrAttributes(?DataWrapperContract $data, int $row): array
    {
        /** @var array<array-key, array<string, mixed>> */
        return (new Collection($this->trAttributes))
            ->flatMap(fn (Closure $callback): array => value($callback, $data, $row, $this))
            ->toArray();
    }

    /**
     * @param  Closure(null|DataWrapperContract<TData> $data, int $row, int $cell, static $table): array<string, string>  $callback
     */
    public function tdAttributes(Closure $callback): static
    {
        $this->tdAttributes[] = $callback;

        return $this;
    }

    /**
     * @param null|DataWrapperContract<TData> $data
     * @return  array<string, mixed>
     */
    public function getTdAttributes(?DataWrapperContract $data, int $row, int $cell): array
    {
        /** @var array<array-key, array<string, mixed>> */
        return (new Collection($this->tdAttributes))
            ->flatMap(fn (Closure $callback): array => value($callback, $data, $row, $cell, $this))
            ->toArray();
    }

    protected function prepareAsyncUrl(Closure|string|null $url = null): Closure|string
    {
        return $url ?? fn (): string => $this->getCore()->getRouter()->getEndpoints()->component(
            $this->getName(),
            additionally: [
                $this->queryParamPrefix . 'filter' => $this->getCore()->getRequest()->get($this->queryParamPrefix . 'filter'),
                $this->queryParamPrefix . 'query-tag' => $this->getCore()->getRequest()->getScalar($this->queryParamPrefix . 'query-tag'),
                $this->queryParamPrefix . 'search' => $this->getCore()->getRequest()->getScalar($this->queryParamPrefix . 'search'),
            ],
        );
    }

    /**
     * @param  array<string, string>  $attributes
     */
    public function headAttributes(array $attributes): self
    {
        $this->headAttributes = $this->headAttributes->merge($attributes);

        return $this;
    }

    /**
     * @param  array<string, string>  $attributes
     */
    public function bodyAttributes(array $attributes): self
    {
        $this->bodyAttributes = $this->bodyAttributes->merge($attributes);

        return $this;
    }

    /**
     * @param  array<string, string>  $attributes
     */
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
        $this->resolvePaginator();

        if ($this->rows instanceof TableRowsContract) {
            return $this->rows;
        }

        if (! \is_null($this->rows)) {
            return $this->rows = \call_user_func($this->rows, $this->resolveRows(), $this);
        }

        return $this->rows = $this->resolveRows();
    }

    public function withoutKey(): static
    {
        $this->isWithoutKey = true;

        return $this;
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
                $this->getRowAsyncAttributes(),
            );
        }

        if (! \is_null($this->reorderableUrl) && $this->isReorderable()) {
            $this->trAttributes(
                $this->getRowReorderAttributes(),
            );
        }

        $rowIndex = $this->getHeadRows()->count();
        $index = 0;

        foreach ($this->getItems() as $data) {
            $casted = $this->castData($data);
            $cells = TableCells::make();

            /** @phpstan-ignore argument.templateType */
            $fields = $this
                ->getFilledFields($casted->toArray(), $casted, $index, $tableFields)
                ->onlyVisible()
                ->when(
                    $this->isReindex() && ! $this->isPreparedReindex(),
                    static fn (FieldsContract $f): FieldsContract => $f->prepareReindexNames(),
                );

            $key = $casted->getKey();

            $tdAttributes = fn (TableCellContract $td): TableCellContract => $td->customAttributes(
                $this->getTdAttributes($casted, $rowIndex, $td->getIndex()),
            );

            $trAttributes = fn (TableRowContract $tr): TableRowContract => $tr->customAttributes(
                $this->getTrAttributes($casted, $rowIndex),
            );

            $buttons = $this->getButtons($casted);
            $hasBulk = ! $this->isPreview() && $this->getBulkButtons()->isNotEmpty();

            if ($this->isVertical()) {
                $components = [];

                foreach ($fields as $field) {
                    $attributes = $field->getWrapperAttributes()->jsonSerialize();
                    $title = Column::make([
                        Div::make([FlexibleRender::make($field->getLabel())])->class('form-label'),
                    ])->columnSpan(\is_int($this->verticalTitleCallback) ? $this->verticalTitleCallback : 2);

                    $value = Column::make([
                        Div::make([
                            $field,
                        ])->customAttributes($attributes),
                    ])->columnSpan(\is_int($this->verticalValueCallback) ? $this->verticalValueCallback : 10);

                    $components[] = Grid::make([
                        \is_null($this->verticalTitleCallback) || \is_int($this->verticalTitleCallback)
                            ? $title
                            : \call_user_func($this->verticalTitleCallback, $field, $title, $this),
                        \is_null($this->verticalValueCallback) || \is_int($this->verticalValueCallback)
                            ? $value
                            : \call_user_func($this->verticalValueCallback, $field, $value, $this),
                    ])->gap(2);
                }

                if ($buttons->isNotEmpty()) {
                    $components[] = Flex::make([
                        $hasBulk ? $this->getRowCheckbox($key, $casted) : null,
                        ActionGroup::make($buttons->toArray()),
                    ])->justifyAlign($hasBulk ? 'between' : 'end');
                }

                $rows->pushRow(
                    TableCells::make([
                        TableTd::make(
                            static fn () => Components::make($components),
                        )->class(['space-elements', 'table-grid'])->when(
                            true,
                            static fn (TableCellContract $td): TableCellContract => $tdAttributes($td)
                        ),
                    ]),
                    key: $this->isWithoutKey ? null : $key,
                    builder: $trAttributes
                );

                $index++;
                $rowIndex++;

                continue;
            }

            $cells
                ->pushCellWhen(
                    $hasBulk,
                    fn (): string => (string) $this->getRowCheckbox($key, $casted),
                    builder: $tdAttributes,
                )
                ->pushFields(
                    $fields,
                    builder: $tdAttributes,
                    startIndex: $hasBulk ? 1 : 0,
                )
                ->pushCellWhen(
                    $this->hasButtons() || $buttons->isNotEmpty(),
                    fn (): string => (string) Flex::make([
                        ActionGroup::make($buttons->toArray())
                            ->when(
                                $this->isStickyButtons(),
                                fn (ActionGroup $actionGroup): ActionGroup => $actionGroup->customAttributes(['strategy' => 'fixed'])
                            ),
                    ])->justifyAlign('end'),
                    index: $fields->count() + ($hasBulk ? 1 : 0),
                    builder: fn (TableCellContract $td): TableCellContract => $tdAttributes(
                        $td->customAttributes(['class' => $this->isStickyButtons() ? $this->getStickyClass() : ''])
                    ),
                );

            $rows->pushRow(
                $cells,
                $this->isWithoutKey ? null : $key,
                builder: $trAttributes,
            );

            $index++;
            $rowIndex++;
        }

        /** @var TableRowsContract */
        return $rows->when(
            $this->isVertical(),
            static fn (TableRowsContract $rows) => $rows->flatten(),
        );
    }

    /**
     * @param Closure(Checkbox $checkbox, DataWrapperContract $data, self $ctx): Checkbox $callback
     */
    public function modifyRowCheckbox(Closure $callback): self
    {
        $this->modifyRowCheckbox = $callback;

        return $this;
    }

    public function getRowCheckbox(int|string|null $key, DataWrapperContract $data): Checkbox
    {
        $checkbox = Checkbox::make('')
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

        if (! \is_null($this->modifyRowCheckbox)) {
            return \call_user_func($this->modifyRowCheckbox, $checkbox, $data, $this);
        }

        return $checkbox;
    }

    public function getRowAsyncAttributes(): Closure
    {
        return fn (?DataWrapperContract $data, int $index): array => \is_null($data)
            ? []
            : [
                AlpineJs::eventBlade(
                    event: JsEvent::TABLE_ROW_UPDATED,
                    name: $this->getName(),
                    params: ListRowEventParams::make($data->getKey() ?? $index)
                ) => 'asyncRowRequest()',
            ];
    }

    public function getRowReorderAttributes(): Closure
    {
        $default = fn (DataWrapperContract $data, int $index) => \is_null($this->reorderableKey)
            ? $data->getKey()
            : data_get($data->getOriginal(), $this->reorderableKey, $index);

        return static fn (?DataWrapperContract $data, int $index): array => [
            'data-id' => \is_null($data) ? $index : $default($data, $index),
        ];
    }

    /**
     * @param  TableRowsContract|Closure(TableRowContract $default): TableRowsContract  $rows
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

        if (! \is_null($this->headRows)) {
            return $this->headRows = \call_user_func($this->headRows, $this->resolveHeadRow(), $this);
        }

        return $this->headRows = TableRows::make([
            $this->resolveHeadRow(),
        ]);
    }

    /**
     * @throws Throwable
     */
    private function resolveHeadRow(): TableRowContract
    {
        $cells = TableCells::make();

        $hasBulk = ! $this->isPreview() && $this->getBulkButtons()->isNotEmpty();
        $index = $hasBulk ? 1 : 0;
        $tdAttributes = fn (int $cell): array => $this->getTdAttributes(null, 0, $cell);

        $cells->pushWhen(
            $hasBulk,
            fn (): TableTh => TableTh::make(
                (string) $this->getRowBulkCheckbox(),
            )
                ->customAttributes($tdAttributes(0))
                ->class(['w-10', 'text-center' => ! $this->isVertical()]),
        );

        if (! $this->isVertical()) {
            $fields = $this->getPreparedFields()->onlyVisible();
            foreach ($fields as $index => $field) {
                $field->sortablePrefix($this->queryParamPrefix);

                $thContent = $field->isSortable() && ! $this->isPreview()
                    ?
                    (string) Link::make(
                        $field->getSortQuery($this->getAsyncUrl()),
                        $field->getLabel(),
                    )
                        ->when(
                            $field->isSortActive(),
                            static fn (Link $link): Link => $link->icon(
                                $field->sortDirectionIs('desc') ? 'bars-arrow-down' : 'bars-arrow-up',
                            ),
                            static fn (Link $link): Link => $link->icon('arrows-up-down'),
                        )
                        ->customAttributes([
                            'class' => $field->isSortActive() ? 'text-primary' : '',
                            '@click.prevent' => $this->isAsync() ? 'asyncRequest' : null,
                        ])
                    : $field->getLabel();

                $cells->push(
                    TableTh::make($thContent, $index)
                        ->customAttributes(['data-column-selection' => $field->getIdentity()])
                        ->customAttributes(['data-column-selection-hide-on-init' => $field->isColumnHideOnInit()])
                        ->customAttributes(['class' => $field->isStickyColumn() ? $this->getStickyClass() : ''])
                        ->customAttributes($tdAttributes($index)),
                );

                $index++;
            }

            $cells->pushWhen(
                $this->hasButtons(),
                fn (): TableTh => TableTh::make('', $index)
                    ->customAttributes($tdAttributes($index))
                    ->customAttributes(['class' => $this->isStickyButtons() ? $this->getStickyClass() : '']),
            );
        }

        return TableRow::make($cells)
            ->customAttributes($this->getTrAttributes(null, 0));
    }

    public function getCellsCount(): int
    {
        $count = $this->hasButtons() ? 1 : 0;
        $count += $this->getPreparedFields()->onlyVisible()->count();

        return $count + ($this->getBulkButtons()->isNotEmpty() ? 1 : 0);
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
     * @param  TableRowsContract|Closure(TableRowContract $default): TableRowsContract  $rows
     */
    public function footRows(TableRowsContract|Closure $rows): self
    {
        $this->footRows = $rows;

        return $this;
    }

    public function inside(string $entity): self
    {
        return $this->customAttributes([
            'data-inside' => $entity,
        ]);
    }

    protected function getFootRows(): TableRowsContract
    {
        if ($this->footRows instanceof TableRowsContract) {
            return $this->footRows;
        }

        if (! \is_null($this->footRows)) {
            return $this->footRows = \call_user_func($this->footRows, $this->resolveFootRow(), $this);
        }

        return $this->footRows = TableRows::make([
            $this->resolveFootRow(),
        ]);
    }

    private function resolveFootRow(): ?TableRowContract
    {
        return $this->getBulkRow()?->customAttributes(
            $this->getTrAttributes(null, $this->getItems()->count() + $this->getHeadRows()->count())
        );
    }

    /**
     * @param  ?Closure(ActionButtonsContract): ActionButtonsContract  $modifyButtons
     */
    public function getBulkRow(?Closure $modifyButtons = null): ?TableRowContract
    {
        if ($this->getBulkButtons()->isEmpty()) {
            return null;
        }

        $buttons = \is_null($modifyButtons) ? $this->getBulkButtons() : $modifyButtons($this->getBulkButtons());

        $cells = TableCells::make()->pushCellWhen(
            ! $this->isPreview(),
            fn (): string => (string) Flex::make([
                ActionGroup::make($buttons->toArray()),
            ])->justifyAlign('start'),
            builder: fn (TableCellContract $td): TableCellContract => $td->customAttributes([
                'colspan' => $this->getCellsCount(),
                ':class' => "\$id('table-component') + '-bulk-actions'",
            ]),
        );

        return TableRow::make($cells)->mergeAttribute(
            ':class',
            "actionsOpen ? 'translate-y-none ease-out' : '-translate-y-full ease-in hidden'",
        );
    }

    /**
     * @param  Closure(self): list<ComponentContract>  $callback
     */
    public function topLeft(Closure $callback): self
    {
        $this->topLeft = $callback;

        return $this;
    }

    /**
     * @param  Closure(self): list<ComponentContract>  $callback
     */
    public function topRight(Closure $callback): self
    {
        $this->topRight = $callback;

        return $this;
    }

    private function getTopLeft(): Components
    {
        $components = \is_null($this->topLeft) ? [] : \call_user_func($this->topLeft);

        return Components::make($components);
    }

    /**
     * @param  array<string, string>  $columns
     */
    private function getTopRight(array $columns = []): Components
    {
        $components = \is_null($this->topRight) ? [] : \call_user_func($this->topRight);

        if ($this->isColumnSelection()) {
            $selectionFields = [];

            foreach ($columns as $column => $label) {
                $selectionFields[] = Switcher::make($label, $column)->customAttributes([
                    'data-column-selection-checker' => true,
                    'data-column' => $column,
                    '@change' => "columnSelection()",
                ]);
            }

            $components[] = Dropdown::make()
                ->strategy('auto')
                ->content(
                    fn (): Div => Div::make($selectionFields)->class('p-2 space-y-2')
                )
                ->toggler(fn (): ActionButton => ActionButton::make('')->icon('table-cells')->square());
        }

        return Components::make($components);
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
                $this->getPaginator()
                    ?->setPageName($this->queryParamPrefix)
                    ?->setPath($this->prepareAsyncUrlFromPaginator()),
            );
        }

        if ($this->isAsync()) {
            $this->customAttributes([
                'data-events' => $this->getAsyncEvents(),
            ]);
        }

        if (! \is_null($this->reorderableUrl) && $this->isReorderable()) {
            $this->customAttributes([
                'data-sortable-url' => $this->reorderableUrl,
                'data-sortable-group' => $this->reorderableGroup,
            ]);
        }

        if ($this->isCreatable() && ! $this->isPreview()) {
            $this->items(
                $this->getItems()->push([null]),
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
        $columns = $this->getFields()->onlyVisible()->flatMap(
            static fn (FieldContract $field): ?array => $field->isColumnSelection()
                ? [$field->getIdentity() => $field->getLabel()]
                : null,
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
                $this->isAsync(),
            ),
            'async' => $this->isAsync(),
            'asyncUrl' => $this->getAsyncUrl(),
            'createButton' => $this->creatableButton,
            'headAttributes' => $this->headAttributes,
            'bodyAttributes' => $this->bodyAttributes,
            'footAttributes' => $this->footAttributes,
            'topLeft' => $this->getTopLeft(),
            'topRight' => $this->getTopRight($columns),
            ...$this->statesToArray(),
        ];
    }
}
