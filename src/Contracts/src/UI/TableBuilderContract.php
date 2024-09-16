<?php

declare(strict_types=1);

namespace MoonShine\Contracts\UI;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\Contracts\Core\Paginator\PaginatorContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\Collection\ActionButtonsContract;
use MoonShine\Contracts\UI\Collection\TableRowsContract;
use MoonShine\Support\Enums\ClickAction;
use Stringable;

/**
 * @mixin Conditionable
 * @mixin HasFieldsContract
 * @mixin HasCasterContract
 */
interface TableBuilderContract extends
    ComponentContract,
    HasAsyncContract
{
    public function getRows(): TableRowsContract;

    public function paginator(PaginatorContract $paginator): static;

    public function hasPaginator(): bool;

    public function isSimplePaginator(): bool;

    public function getPaginator(bool $async = false): ?PaginatorContract;

    public function getButtons(DataWrapperContract $data): ActionButtonsContract;

    public function buttons(iterable $buttons = []): static;

    public function hasButtons(): bool;

    public function getBulkButtons(): ActionButtonsContract;

    public function getItems(): Collection;

    public function items(iterable $items = []): static;

    public function creatable(
        bool $reindex = true,
        ?int $limit = null,
        ?string $label = null,
        ?string $icon = null,
        array $attributes = [],
        ?ActionButtonContract $button = null,
    ): static;

    public function isCreatable(): bool;

    public function hasNotFound(): bool;

    public function withNotFound(): static;

    public function preview(): static;

    public function isPreview(): bool;

    public function editable(): static;

    public function isEditable(): bool;

    public function vertical(): static;

    public function isVertical(): bool;

    public function reindex(bool $prepared = false): static;

    public function isReindex(): bool;

    public function isPreparedReindex(): bool;

    public function reorderable(
        ?string $url = null,
        string $key = 'id',
        ?string $group = null
    ): static;

    public function isReorderable(): bool;

    public function simple(): static;

    public function isSimple(): bool;

    public function searchable(): static;

    public function isSearchable(): bool;

    public function sticky(): static;

    public function isSticky(): bool;

    public function columnSelection(): static;

    public function isColumnSelection(): bool;

    public function clickAction(?ClickAction $action = null, ?string $selector = null): static;

    public function pushState(): static;

    public function removeAfterClone(): static;
}
