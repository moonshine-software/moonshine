<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\Collection\ActionButtonsContract;
use MoonShine\Contracts\UI\Collection\TableRowsContract;
use MoonShine\Support\Enums\ClickAction;

/**
 * @template TData of mixed = mixed
 * @mixin Conditionable
 * @mixin HasFieldsContract
 * @mixin HasCasterContract<DataCasterContract<TData>, DataWrapperContract<TData>>
 */
interface TableBuilderContract extends
    ComponentContract,
    HasButtonsContract,
    HasAsyncContract
{
    /**
     * @param  TableRowsContract|Closure(TableRowsContract $default): TableRowsContract  $rows
     */
    public function rows(TableRowsContract|Closure $rows): self;

    public function getRows(): TableRowsContract;

    /**
     * @param  TableRowsContract|Closure(TableRowContract $default): TableRowsContract  $rows
     */
    public function headRows(TableRowsContract|Closure $rows): self;

    /**
     * @param  TableRowsContract|Closure(TableRowContract $default): TableRowsContract  $rows
     */
    public function footRows(TableRowsContract|Closure $rows): self;

    /**
     * @param  list<TData>  $items
     *
     */
    public function items(iterable $items = []): static;

    /**
     * @return iterable<array-key, TData>
     */
    public function getItems(): iterable;

    /**
     * @param  Closure(null|DataWrapperContract<TData> $data, int $row, int $cell, static $table): array<string, string>  $callback
     */
    public function tdAttributes(Closure $callback): static;

    /**
     * @param null|DataWrapperContract<TData> $data
     * @return  array<string, string>
     */
    public function getTdAttributes(?DataWrapperContract $data, int $row, int $cell): array;

    /**
     * @param  Closure(null|DataWrapperContract<TData> $data, int $row, static $table): array<string, string>  $callback
     */
    public function trAttributes(Closure $callback): static;

    /**
     * @param null|DataWrapperContract<TData> $data
     * @return  array<string, string>
     */
    public function getTrAttributes(?DataWrapperContract $data, int $row): array;

    /**
     * @param  array<string, string>  $attributes
     */
    public function headAttributes(array $attributes): self;

    /**
     * @param  array<string, string>  $attributes
     */
    public function bodyAttributes(array $attributes): self;

    /**
     * @param  array<string, string>  $attributes
     */
    public function footAttributes(array $attributes): self;

    public function getCellsCount(): int;

    /**
     * @param  Closure(self): list<ComponentContract>  $callback
     */
    public function topLeft(Closure $callback): self;

    /**
     * @param  Closure(self): list<ComponentContract>  $callback
     */
    public function topRight(Closure $callback): self;

    /**
     * @param  ?Closure(ActionButtonsContract): ActionButtonsContract  $modifyButtons
     */
    public function getBulkRow(?Closure $modifyButtons = null): ?TableRowContract;

    /**
     * States
     */

    /**
     * @param  array<string, string>  $attributes
     */
    public function creatable(
        bool $reindex = true,
        ?int $limit = null,
        ?string $label = null,
        ?string $icon = null,
        array $attributes = [],
        ?ActionButtonContract $button = null,
    ): static;

    public function isCreatable(): bool;

    public function queryParamPrefix(string $prefix): static;

    public function withFilters(string $formName): static;

    public function hasNotFound(): bool;

    public function withNotFound(): static;

    public function preview(): static;

    public function isPreview(): bool;

    public function editable(): static;

    public function isEditable(): bool;

    /**
     * @param  null|int|Closure(FieldContract $field, ComponentContract $default, static $ctx): ComponentContract  $title
     * @param  null|int|Closure(FieldContract $field, ComponentContract $default, static $ctx): ComponentContract  $value
     */
    public function vertical(null|Closure|int $title = null, null|Closure|int $value = null): static;

    public function isVertical(): bool;

    public function inside(string $entity): self;

    public function reindex(bool $prepared = false): static;

    public function isReindex(): bool;

    public function isPreparedReindex(): bool;

    public function reorderable(
        ?string $url = null,
        ?string $key = null,
        ?string $group = null
    ): static;

    public function isReorderable(): bool;

    public function simple(): static;

    public function isSimple(): bool;

    public function searchable(): static;

    public function isSearchable(): bool;

    public function sticky(): static;

    public function isSticky(): bool;

    public function stickyButtons(): static;

    public function isStickyButtons(): bool;

    public function getStickyClass(): string;

    public function columnSelection(): static;

    public function isColumnSelection(): bool;

    public function lazy(): static;

    public function isLazy(): bool;

    public function skeleton(Closure|bool|null $condition = null): static;

    public function hasSkeleton(): bool;

    public function loader(Closure|bool|null $condition = null): static;

    public function hasLoader(): bool;

    /**
     * @return array{
     *     preview: bool,
     *     notfound: bool,
     *     creatable: bool,
     *     reindex: bool,
     *     reorderable: bool,
     *     simple: bool,
     *     sticky: bool,
     *     stickyButtons: bool,
     *     searchable: bool,
     *     searchValue: string,
     *     columnSelection: bool,
     *     skeleton: bool,
     *     loader: bool,
     * }
     */
    public function statesToArray(): array;

    public function clickAction(?ClickAction $action = null, ?string $selector = null): static;

    public function pushState(): static;

    public function removeAfterClone(): static;

    public function withoutKey(): static;
}
