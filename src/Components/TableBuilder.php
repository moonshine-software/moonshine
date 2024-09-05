<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\View\ComponentAttributeBag;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Contracts\Table\TableContract;
use MoonShine\Enums\JsEvent;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Td;
use MoonShine\Support\AlpineJs;
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

    protected ?Closure $thead = null;

    protected ?Closure $tbody = null;

    protected ?Closure $bodyBefore = null;

    protected ?Closure $bodyAfter = null;

    protected ?Closure $tfoot = null;

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

        return $this->getItems()->filter()->map(function (mixed $data, int $index) use ($tableFields, $hasBulk): TableRow {
            $casted = $this->castData($data);
            $raw = $this->unCastData($data);

            $fields = $this
                ->getFilledFields($raw, $casted, $index, $tableFields)
                ->onlyVisible()
                ->when(
                    $this->isReindex() && ! $this->isPreparedReindex(),
                    fn (Fields $f): Fields => $f->prepareReindex()
                )
            ;

            $fields->each(function ($field, $cellIndex) use ($hasBulk): void {
                if ($field instanceof Td && $field->hasTdAttributes()) {
                    $this->tdAttributes(
                        function ($data, $row, $cell, ComponentAttributeBag $attr) use ($field, $cellIndex, $hasBulk): ComponentAttributeBag {
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
     * @param  Closure(mixed $data, int $row, ComponentAttributeBag $attributes, self $table): ComponentAttributeBag $closure
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

    /**
     * @param  Closure(mixed $data, int $row, ComponentAttributeBag $attributes, self $table): ComponentAttributeBag $closure
     */
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
     * @param  Closure(mixed $data, int $row, int $cell, ComponentAttributeBag $attributes, self $table): ComponentAttributeBag $closure
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

    protected function prepareAsyncUrl(Closure|string|null $asyncUrl = null): Closure|string|null
    {
        return $asyncUrl ?? fn (): string => moonshineRouter()->asyncTable($this->getName());
    }

    public function performBeforeRender(): self
    {
        if ($this->isAsync() && $this->hasPaginator()) {
            $this->getPaginator()
                ?->appends(request()->except('page'))
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
            function (mixed $data, int $index, ComponentAttributeBag $attr, TableRow $row) use ($systemTrEvents) {
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

    /**
     * @param  Closure(Fields $fields, self $ctx): string  $thead
     */
    public function thead(Closure $thead): self
    {
        $this->thead = $thead;

        return $this;
    }

    /**
     * @param  Closure(Collection $rows, self $ctx): string  $tbody
     */
    public function tbody(Closure $tbody): self
    {
        $this->tbody = $tbody;

        return $this;
    }

    /**
     * @param  Closure(ActionButtons $bulkButtons, self $ctx): string  $tfoot
     */
    public function tfoot(Closure $tfoot): self
    {
        $this->tfoot = $tfoot;

        return $this;
    }

    /**
     * @param  Closure(Collection $rows, self $ctx): string  $before
     */
    public function bodyBefore(Closure $before): self
    {
        $this->bodyBefore = $before;

        return $this;
    }

    /**
     * @param  Closure(Collection $rows, self $ctx): string  $after
     */
    public function bodyAfter(Closure $after): self
    {
        $this->bodyAfter = $after;

        return $this;
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    protected function viewData(): array
    {
        $this->performBeforeRender();
        $fields = $this->preparedFields()->onlyVisible();
        $rows = $this->rows();
        $bulkButtons = $this->getBulkButtons();

        return [
                'rows' => $rows,
                'fields' => $fields,
                'thead' => value($this->thead, $fields, $this),
                'tbody' => value($this->tbody, $rows, $this),
                'bodyBefore' => value($this->bodyBefore, $rows, $this),
                'bodyAfter' => value($this->bodyAfter, $rows, $this),
                'tfoot' => value($this->tfoot, $bulkButtons, $this),
                'name' => $this->getName(),
                'hasPaginator' => $this->hasPaginator(),
                'simple' => $this->isSimple(),
                'simplePaginate' => ! $this->getPaginator() instanceof LengthAwarePaginator,
                'paginator' => $this->getPaginator(),
                'bulkButtons' => $bulkButtons,
                'async' => $this->isAsync(),
                'asyncUrl' => $this->asyncUrl(),
                'createButton' => $this->creatableButton,
                'sticky' => $this->isSticky(),
            ] + $this->statesToArray();
    }
}
