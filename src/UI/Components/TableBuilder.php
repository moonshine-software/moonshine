<?php

declare(strict_types=1);

namespace MoonShine\UI\Components;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Table\TableContract;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Components\MoonShineComponentAttributeBag;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Support\Traits\HasAsync;
use MoonShine\UI\Collections\Fields;
use MoonShine\UI\Components\Table\TableRow;
use MoonShine\UI\Fields\Field;
use MoonShine\UI\Fields\Td;
use MoonShine\UI\Traits\Table\TableStates;
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
        parent::__construct();

        $this->fields($fields);
        $this->items($items);

        if (! is_null($paginator)) {
            $this->paginator($paginator);
        }

        $this->withAttributes([]);
    }

    public function preparedFields(): Fields
    {
        return $this->getFields()->values();
    }

    /**
     * @return Collection<int, TableRow>
     * @throws Throwable
     */
    public function rows(): Collection
    {
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

        return $this->getItems()->filter()->map(function (mixed $data, int $index) use ($tableFields, $hasBulk): TableRow {
            $casted = $this->castData($data);
            $raw = $this->unCastData($data);

            $fields = $this
                ->getFilledFields($raw, $casted, $index, $tableFields)
                ->when(
                    $this->isReindex() && ! $this->isPreparedReindex(),
                    fn (Fields $f): Fields => $f->prepareReindex()
                );

            $fields->each(function ($field, $cellIndex) use($hasBulk): void {
                if($field instanceof Td && $field->hasTdAttributes()) {
                    $this->tdAttributes(
                        function ($data, $row, $cell, MoonShineComponentAttributeBag $attr) use ($field, $cellIndex, $hasBulk): MoonShineComponentAttributeBag {
                            $cell = $hasBulk ? $cell - 1 : $cell;

                            return $cellIndex === $cell
                                ? $field->resolveTdAttributes($data, $attr)
                                : $attr;
                        }
                    );
                }
            });

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
     * @param  Closure(mixed $data, int $row, MoonShineComponentAttributeBag $attributes, $table self): MoonShineComponentAttributeBag $closure
     */
    public function trAttributes(Closure $closure): self
    {
        $this->trAttributes = $closure;

        return $this;
    }

    public function getTrAttributes(): ?Closure
    {
        return $this->trAttributes;
    }

    protected function systemTrAttributes(Closure $closure): self
    {
        $this->systemTrAttributes = $closure;

        return $this;
    }

    public function getSystemTrAttributes(): ?Closure
    {
        return $this->systemTrAttributes;
    }

    /**
     * @param  Closure(mixed $data, int $row, int $cell, MoonShineComponentAttributeBag $attributes, $table self): MoonShineComponentAttributeBag $closure
     */
    public function tdAttributes(Closure $closure): self
    {
        $this->tdAttributes = $closure;

        return $this;
    }

    public function getTdAttributes(): ?Closure
    {
        return $this->tdAttributes;
    }

    protected function prepareAsyncUrl(Closure|string|null $url = null): Closure|string|null
    {
        return $url ?? fn (): string => moonshineRouter()->asyncComponent(
            $this->getName(),
            additionally: [
                'filters' => moonshine()->getRequest()->get('filters'),
                'query-tag' => moonshine()->getRequest()->get('query-tag'),
                'search' => moonshine()->getRequest()->get('search'),
            ]
        );
    }

    public function performBeforeRender(): self
    {
        if ($this->isAsync() && $this->hasPaginator()) {
            $this->getPaginator()
                ?->appends(moonshine()->getRequest()->getExcept('page'))
                ?->setPath($this->prepareAsyncUrlFromPaginator());
        }

        $systemTrEvents = [];

        if ($this->isAsync()) {
            $this->customAttributes([
                'data-events' => $this->asyncEvents(),
            ]);

            $systemTrEvents[] = fn (mixed $data, TableRow $row, int $index): array => $row->getKey() ? [
                AlpineJs::eventBlade(
                    JsEvent::TABLE_ROW_UPDATED,
                    "{$this->getName()}-{$row->getKey()}",
                ) => "asyncRowRequest(`{$row->getKey()}`,`$index`)",
            ] : [];
        }

        if (! is_null($this->sortableUrl) && $this->isSortable()) {
            $this->customAttributes([
                'data-sortable-url' => $this->sortableUrl,
                'data-sortable-group' => $this->sortableGroup,
            ]);

            $systemTrEvents[] = fn (mixed $data, TableRow $row, int $index): array => [
                'data-id' => data_get($data, $this->sortableKey ?? 'id', $index),
            ];
        }

        $this->systemTrAttributes(
            function (mixed $data, int $index, MoonShineComponentAttributeBag $attr, TableRow $row) use (
                $systemTrEvents
            ) {
                foreach ($systemTrEvents as $systemTrEvent) {
                    $attr = $attr->merge($systemTrEvent($data, $row, $index));
                }

                return $attr;
            }
        );

        if ($this->isCreatable() && ! $this->isPreview()) {
            $this->items(
                $this->getItems()->push([null])
            );
        }

        return $this;
    }

    protected function prepareBeforeRender(): void
    {
        parent::prepareBeforeRender();

        $this->performBeforeRender();
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        return [
            'rows' => $this->rows(),
            'fields' => $this->preparedFields(),
            'name' => $this->getName(),
            'hasPaginator' => $this->hasPaginator(),
            'simple' => $this->isSimple(),
            'simplePaginate' => ! $this->getPaginator() instanceof LengthAwarePaginator,
            'paginator' => $this->getPaginator(),
            'bulkButtons' => $this->getBulkButtons(),
            'async' => $this->isAsync(),
            'asyncUrl' => $this->getAsyncUrl(),
            'createButton' => $this->creatableButton,
            ...$this->statesToArray(),
        ];
    }
}
