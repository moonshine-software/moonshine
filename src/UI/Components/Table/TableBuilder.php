<?php

declare(strict_types=1);

namespace MoonShine\UI\Components\Table;

use Closure;
use Illuminate\Contracts\Pagination\Paginator;
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
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Traits\Table\TableStates;
use Throwable;

/**
 * @method static static make(Fields|array $fields = [], iterable $items = [], ?Paginator $paginator = null)
 */
final class TableBuilder extends IterableComponent implements TableContract
{
    use TableStates;
    use HasAsync;

    protected string $view = 'moonshine::components.table.builder';

    protected ?TableRows $rows = null;

    protected ?TableRows $headRows = null;

    protected ?TableRows $bodyRows = null;

    protected ?TableRows $footRows = null;

    protected array $trAttributes = [];

    protected array $tdAttributes = [];

    protected MoonShineComponentAttributeBag $headAttributes;

    protected MoonShineComponentAttributeBag $bodyAttributes;

    protected MoonShineComponentAttributeBag $footAttributes;

    public function __construct(
        Fields|array $fields = [],
        iterable $items = [],
        ?Paginator $paginator = null
    ) {
        parent::__construct();

        $this->fields($fields);
        $this->items($items);

        if (! is_null($paginator)) {
            $this->paginator($paginator);
        }

        $this->withAttributes([]);

        $this->headAttributes = new MoonShineComponentAttributeBag();
        $this->bodyAttributes = new MoonShineComponentAttributeBag();
        $this->footAttributes = new MoonShineComponentAttributeBag();
    }

    public function preparedFields(): FieldsCollection
    {
        return $this->getFields()->values();
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

    public function rows(TableRows $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    public function getRows(): TableRows
    {
        if(!is_null($this->rows)) {
            return $this->rows;
        }

        $tableFields = $this->preparedFields();
        $hasBulk = $this->getBulkButtons()->isNotEmpty();

        if (! $this->isEditable()) {
            $tableFields = $tableFields
                ->onlyFields(withWrappers: true)
                ->map(
                    fn (Field $field): Field => $field
                        ->withoutWrapper()
                        ->forcePreview()
                );
        }

        $rows = TableRows::make();

        if($this->isAsync()) {
            $this->trAttributes(fn (?CastedData $data, int $index): array => $data?->getKey() ? [
                AlpineJs::eventBlade(
                    JsEvent::TABLE_ROW_UPDATED,
                    "{$this->getName()}-{$data->getKey()}",
                ) => "asyncRowRequest(`{$data->getKey()}`,`$index`)",
            ] : []);
        }

        if(! is_null($this->sortableUrl) && $this->isSortable()) {
            $this->trAttributes(fn (mixed $data, int $index): array => [
                'data-id' => data_get($data, $this->sortableKey ?? 'id', $index),
            ]);
        }

        foreach ($this->getItems()->filter() as $index => $data) {
            $casted = $this->castData($data);
            $cells = TableCells::make();

            $fields = $this
                ->getFilledFields($casted->toArray(), $casted, $index, $tableFields)
                ->when(
                    $this->isReindex() && ! $this->isPreparedReindex(),
                    fn (Fields $f): Fields => $f->prepareReindex()
                );

            $id = $fields->findByClass(ID::class);
            $key = $id?->value();

            if ($this->isVertical()) {
                foreach ($fields as $cellIndex => $field) {
                    $cells = TableCells::make()
                        ->pushCell($field->getLabel(), builder: fn (TableTd $td) => $td->customAttributes([
                            'width' => '20%',
                            'class' => 'font-semibold',
                        ]))
                        ->pushCell((string) $field);

                    $rows->pushRow($cells, $cellIndex);
                }

                continue;
            }

            $cells
                ->pushCellWhen(
                    !$this->isPreview() && $hasBulk,
                    fn() => (string) Checkbox::make('')
                        ->setValue($key)
                        ->setNameAttribute("items[{$key}]")
                        ->withoutWrapper()
                        ->simpleMode()
                        ->customAttributes([
                            'autocomplete' => 'off',
                            '@change' => "actions('row', \$id('table-component'))",
                            ':class' => "\$id('table-component') + '-tableActionRow'",
                            'class' => 'tableActionRow',
                        ])
                )
                ->pushFields(
                    $fields,
                    builder: fn (TableTd $td) => $td->customAttributes(
                        $this->getTdAttributes($casted, $index, $td->getIndex())
                    )
                )
                ->pushCell(
                    (string) Flex::make([
                        ActionGroup::make($this->getButtons($casted)->toArray()),
                    ])->justifyAlign('end')
                );

            $rows->pushRow(
                $cells,
                $key,
                builder: fn (TableRow $tr) => $tr->customAttributes($this->getTrAttributes($casted, $index))
            );
        }

        return $this->rows = $rows->when(
            $this->isVertical(),
            fn (TableRows $rows) => $rows->flatten()
        );
    }

    public function headRows(TableRows $rows): self
    {
        $this->headRows = $rows;

        return $this;
    }

    protected function getHeadRows(): TableRows
    {
        if(!is_null($this->headRows)) {
            return $this->headRows;
        }

        $tableFields = $this->preparedFields();

        $cells = TableCells::make();
        $rows = TableRows::make();

        if (! $this->isVertical()) {
            $cells->pushWhen(
                !$this->isPreview() && $this->getBulkButtons()->isNotEmpty(),
                fn() => TableTh::make(
                    (string) Checkbox::make('')
                        ->withoutWrapper()
                        ->simpleMode()
                        ->customAttributes([
                            'autocomplete' => 'off',
                            '@change' => "actions('all', \$id('table-component'))",
                            ':class' => "\$id('table-component') + '-actionsAllChecked'",
                        ])
                        ->class('actionsAllChecked')
                )->class('w-10 text-center')
            );

            foreach ($tableFields as $field) {
                $cells->push(
                    $field->isSortable() && !$this->isPreview()
                        ?
                        TableTh::make(
                            (string) Link::make(
                                $field->sortQuery($this->getAsyncUrl()),
                                $field->getLabel()
                            )
                                ->icon(
                                    $field->sortActive() && $field->sortDirection('desc') ? 'bars-arrow-down'
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
                static fn() => TableTh::make('')
            );
        }

        return $this->headRows = $rows->pushRow(
            $cells,
            0
        );
    }

    public function footRows(TableRows $rows): self
    {
        $this->footRows = $rows;

        return $this;
    }

    protected function getFootRows(): TableRows
    {
        if(!is_null($this->footRows)) {
            return $this->footRows;
        }

        $cells = TableCells::make();

        $cells->pushWhen(
            !$this->isPreview(),
            fn() => TableTd::make(
                (string) Flex::make([
                    ActionGroup::make($this->getBulkButtons()->toArray()),
                ])->justifyAlign('start')
            )->customAttributes([
                'colspan' => 6,
                ':class' => "\$id('table-component') + '-bulkActions'",
            ])
        );

        return $this->footRows = TableRows::make([
            TableRow::make($cells),
        ]);
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->performBeforeRender();
    }

    protected function performBeforeRender(): self
    {
        if ($this->isAsync() && $this->hasPaginator()) {
            $this->getPaginator()
                ?->appends(moonshine()->getRequest()->getExcept('page'))
                ?->setPath($this->prepareAsyncUrlFromPaginator());
        }

        if ($this->isAsync()) {
            $this->customAttributes([
                'data-events' => $this->asyncEvents(),
            ]);
        }

        if (! is_null($this->sortableUrl) && $this->isSortable()) {
            $this->customAttributes([
                'data-sortable-url' => $this->sortableUrl,
                'data-sortable-group' => $this->sortableGroup,
            ]);
        }

        if ($this->isCreatable() && ! $this->isPreview()) {
            $this->items(
                $this->getItems()->push([null])
            );
        }

        $this->footAttributes([
            ':class' => "actionsOpen ? 'translate-y-none ease-out' : '-translate-y-full ease-in hidden'"
        ]);

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
            'simplePaginate' => $this->isSimplePaginator(),
            'paginator' => $this->getPaginator(),
            'bulkButtons' => $this->getBulkButtons(),
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
