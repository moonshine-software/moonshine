<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Resources;

use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Core\Resources\Resource;
use MoonShine\Core\TypeCasts\MixedDataCaster;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Traits\Resource\ResourceActions;
use MoonShine\Laravel\Traits\Resource\ResourceCrudRouter;
use MoonShine\Laravel\Traits\Resource\ResourceEvents;
use MoonShine\Laravel\Traits\Resource\ResourceQuery;
use MoonShine\Laravel\Traits\Resource\ResourceValidation;
use MoonShine\Laravel\Traits\Resource\ResourceWithAuthorization;
use MoonShine\Laravel\Traits\Resource\ResourceWithButtons;
use MoonShine\Laravel\Traits\Resource\ResourceWithFields;
use MoonShine\Laravel\Traits\Resource\ResourceWithPageComponents;
use MoonShine\Laravel\Traits\Resource\ResourceWithTableModifiers;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\ClickAction;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;

/**
 * @template-covariant TCaster of DataCasterContract
 * @template-covariant TCastedData of DataWrapperContract
 * @template-covariant TData of mixed
 */
abstract class CrudResource extends Resource implements CrudResourceContract
{
    use ResourceWithFields;
    use ResourceWithButtons;
    use ResourceWithTableModifiers;
    use ResourceWithPageComponents;

    use ResourceActions;
    use ResourceWithAuthorization;

    /** @use ResourceValidation<TData> */
    use ResourceValidation;
    /** @use ResourceCrudRouter<TData> */
    use ResourceCrudRouter;
    /** @use ResourceEvents<TData> */
    use ResourceEvents;

    /** @use ResourceQuery<TData> */
    use ResourceQuery;

    protected string $column = 'id';

    protected bool $createInModal = false;

    protected bool $editInModal = false;

    protected bool $detailInModal = false;

    protected bool $isAsync = true;

    protected bool $isPrecognitive = false;

    protected bool $deleteRelationships = false;

    protected bool $submitShowWhen = false;

    /**
     * The click action to use when clicking on the resource in the table.
     */
    protected ?ClickAction $clickAction = null;

    protected bool $stickyTable = false;

    protected bool $columnSelection = false;

    protected ?string $casterKeyName = null;

    protected bool $isRecentlyCreated = false;

    /**
     * @param array<int, int> $ids
     */
    abstract public function massDelete(array $ids): void;

    /**
     * @param TData $item
     */
    abstract public function delete(mixed $item, ?FieldsContract $fields = null): bool;

    /**
     * @param TData $item
     * @return TData
     */
    abstract public function save(mixed $item, ?FieldsContract $fields = null): mixed;

    public function isRecentlyCreated(): bool
    {
        return $this->isRecentlyCreated;
    }

    public function flushState(): void
    {
        $this->item = null;
        $this->itemID = null;
        $this->pages = null;
    }

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            IndexPage::class,
            FormPage::class,
            DetailPage::class,
        ];
    }

    public function getIndexPage(): ?PageContract
    {
        return $this->getPages()->indexPage();
    }

    public function getFormPage(): ?PageContract
    {
        return $this->getPages()->formPage();
    }

    public function getDetailPage(): ?PageContract
    {
        return $this->getPages()->detailPage();
    }

    public function getCaster(): DataCasterContract
    {
        return new MixedDataCaster($this->casterKeyName);
    }

    /**
     * @return ?DataWrapperContract<TData>
     */
    public function getCastedData(): ?DataWrapperContract
    {
        if (is_null($this->getItem())) {
            return null;
        }

        return $this->getCaster()->cast($this->getItem());
    }

    /**
     * @return TData
     */
    public function getDataInstance(): mixed
    {
        return [];
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function isCreateInModal(): bool
    {
        return $this->createInModal;
    }

    public function isEditInModal(): bool
    {
        return $this->editInModal;
    }

    public function isDetailInModal(): bool
    {
        return $this->detailInModal;
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    public function isPrecognitive(): bool
    {
        return $this->isPrecognitive;
    }

    public function isDeleteRelationships(): bool
    {
        return $this->deleteRelationships;
    }

    public function getClickAction(): ?string
    {
        return $this->clickAction?->value;
    }

    public function isStickyTable(): bool
    {
        return $this->stickyTable;
    }

    public function isColumnSelection(): bool
    {
        return $this->columnSelection;
    }

    public function isSubmitShowWhen(): bool
    {
        return $this->submitShowWhen;
    }

    /**
     * @return list<Metric>
     */
    protected function metrics(): array
    {
        return [];
    }

    /**
     * @return list<Metric>
     */
    public function getMetrics(): array
    {
        return $this->metrics();
    }

    /**
     * @return string[]
     */
    protected function search(): array
    {
        return ['id'];
    }

    public function hasSearch(): bool
    {
        return $this->search() !== [];
    }

    /**
     * @return string[]
     */
    public function getSearchColumns(): array
    {
        return $this->search();
    }

    public function getListComponentName(): string
    {
        return rescue(
            fn (): string => $this->getIndexPage()?->getListComponentName(),
            "index-table-{$this->getUriKey()}",
            false
        );
    }

    public function isListComponentRequest(): bool
    {
        return request()->ajax() && request()->input('_component_name') === $this->getListComponentName();
    }

    public function getListEventName(?string $name = null, array $params = []): string
    {
        $name ??= $this->getListComponentName();

        return rescue(
            fn (): string => AlpineJs::event($this->getIndexPage()?->getListEventName() ?? '', $name, $params),
            AlpineJs::event(JsEvent::TABLE_UPDATED, $name, $params),
            false
        );
    }

    /**
     * @param TData $item
     */
    public function modifyResponse(mixed $item): mixed
    {
        return $item;
    }

    /**
     * @param  iterable<TData>  $items
     */
    public function modifyCollectionResponse(mixed $items): mixed
    {
        return $items;
    }
}
