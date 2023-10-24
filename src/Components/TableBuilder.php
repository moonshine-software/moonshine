<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MoonShine\ActionButtons\ActionButtons;
use MoonShine\Contracts\Table\TableContract;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Table\TableRow;
use MoonShine\Traits\HasAsync;
use MoonShine\Traits\Table\TableStates;

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
        return $this->getItems()->map(function (mixed $data, $index): TableRow {
            $casted = $this->castData($data);
            $raw = $this->unCastData($data);

            $fields = $this->getFields();

            if (! is_null($this->getName())) {
                $fields->onlyFields()->each(
                    fn (Field $field): Field => $field->formName($this->getName())
                );
            }

            return TableRow::make(
                $casted,
                $fields->fillCloned($raw, $casted, $index),
                $this->getButtons($casted),
                $this->trAttributes,
                $this->tdAttributes
            );
        });
    }

    public function getBulkButtons(): ActionButtons
    {
        return ActionButtons::make($this->buttons)
            ->bulk();
    }

    public function trAttributes(Closure $closure): self
    {
        $this->trAttributes = $closure;

        return $this;
    }

    public function tdAttributes(Closure $closure): self
    {
        $this->tdAttributes = $closure;

        return $this;
    }

    protected function prepareAsyncUrlFromPaginator(): string
    {
        $hostAsyncUrl = strtok($this->asyncUrl(), '?');

        if(!$hostAsyncUrl) {
            return $this->asyncUrl();
        }

        parse_str(parse_url($this->asyncUrl(), PHP_URL_QUERY), $asyncUri);

        $paginatorUri = $this->getPaginator()->resolveQueryString();

        $asyncUri = array_filter($asyncUri, function ($value, $key) use ($paginatorUri) {
            return !isset($paginatorUri[$key]);
        }, ARRAY_FILTER_USE_BOTH);

        if(count($asyncUri)) {
            return $hostAsyncUrl . "?" . Arr::query($asyncUri);
        }

        return $hostAsyncUrl;
    }

    protected function viewData(): array
    {
        if($this->isAsync() && $this->hasPaginator()) {
            $this->getPaginator()
                ?->setPath($this->prepareAsyncUrlFromPaginator());
        }

        return [
            'rows' => $this->rows(),
            'fields' => $this->getFields(),
            'name' => $this->getName(),
            'hasPaginator' => $this->hasPaginator(),
            'simple' => $this->isSimple(),
            'paginator' => $this->getPaginator(),
            'bulkButtons' => $this->getBulkButtons(),
            'async' => $this->isAsync(),
            'asyncUrl' => $this->asyncUrl(),
        ] + $this->statesToArray();
    }
}
