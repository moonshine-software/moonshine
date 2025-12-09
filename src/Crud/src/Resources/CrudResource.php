<?php

declare(strict_types=1);

namespace MoonShine\Crud\Resources;

use Closure;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use MoonShine\Contracts\Core\CrudPageContract;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\TypeCasts\DataCasterContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use MoonShine\Contracts\UI\ModalContract;
use MoonShine\Contracts\UI\OffCanvasContract;
use MoonShine\Contracts\UI\TableBuilderContract;
use MoonShine\Core\Resources\Resource;
use MoonShine\Core\TypeCasts\MixedDataCaster;
use MoonShine\Crud\Collections\Fields;
use MoonShine\Crud\Concerns\Resource\HasCrudResponseModifiers;
use MoonShine\Crud\Concerns\Resource\HasFilters;
use MoonShine\Crud\Concerns\Resource\HasHandlers;
use MoonShine\Crud\Concerns\Resource\HasListComponent;
use MoonShine\Crud\Concerns\Resource\HasQueryTags;
use MoonShine\Crud\Contracts\HasFiltersContract;
use MoonShine\Crud\Contracts\HasHandlersContract;
use MoonShine\Crud\Contracts\HasQueryTagsContract;
use MoonShine\Crud\Contracts\Page\DetailPageContract;
use MoonShine\Crud\Contracts\Page\FormPageContract;
use MoonShine\Crud\Contracts\Page\IndexPageContract;
use MoonShine\Crud\Contracts\Resource\HasCrudResponseModifiersContract;
use MoonShine\Crud\Traits\Resource\ResourceActions;
use MoonShine\Crud\Traits\Resource\ResourceCrudRouter;
use MoonShine\Crud\Traits\Resource\ResourceEvents;
use MoonShine\Crud\Traits\Resource\ResourceQuery;
use MoonShine\Crud\Traits\Resource\ResourceWithAuthorization;
use MoonShine\Crud\Traits\Resource\ResourceWithButtons;
use MoonShine\Crud\Traits\Resource\ResourceWithFields;
use Throwable;

/**
 * @template TCore of CoreContract = CoreContract
 * @template TData of mixed = mixed
 * @template-covariant TIndexPage of null|CrudPageContract = null
 * @template-covariant TFormPage of null|CrudPageContract = null
 * @template-covariant TDetailPage of null|CrudPageContract = null
 * @template TException of Throwable = \Throwable
 * @template TFields of Fields = Fields
 *
 * @implements CrudResourceContract<TCore, TData, TIndexPage, TFormPage, TDetailPage, TException, TFields>
 * @extends Resource<CrudPageContract, TCore>
 * @implements HasFiltersContract<TFields>
 */
abstract class CrudResource extends Resource implements
    CrudResourceContract,
    HasQueryTagsContract,
    HasHandlersContract,
    HasFiltersContract,
    HasCrudResponseModifiersContract
{
    /** @use HasFilters<TFields> */
    use HasFilters;
    use HasHandlers;
    use HasQueryTags;
    use HasCrudResponseModifiers;

    use HasListComponent;

    use ResourceWithButtons;
    use ResourceActions;
    use ResourceWithAuthorization;

    /** @use ResourceWithFields<TFields> */
    use ResourceWithFields;

    /** @use ResourceCrudRouter<TData> */
    use ResourceCrudRouter;

    /** @use ResourceEvents<TData> */
    use ResourceEvents;

    /** @use ResourceQuery<TData, TFields> */
    use ResourceQuery;

    protected string $column = 'id';

    protected bool $deleteRelationships = false;

    protected ?string $casterKeyName = null;

    protected bool $isRecentlyCreated = false;

    protected ?PageContract $activePage = null;

    protected bool $isAsync = true;

    protected bool $createInModal = false;

    protected bool $editInModal = false;

    protected bool $detailInModal = false;

    /**
     * @param bool $orFail
     *
     * @return DataWrapperContract
     * @throws Throwable
     */
    abstract public function findItem(bool $orFail = false): ?DataWrapperContract;

    /**
     * @return iterable<TData>|Collection<array-key, TData>|LazyCollection<array-key, TData>|CursorPaginator<array-key, TData>|Paginator<array-key, TData>
     */
    abstract public function getItems(): iterable|Collection|LazyCollection|CursorPaginator|Paginator;

    /**
     * @param  array<int|string>  $ids
     */
    abstract public function massDelete(array $ids): void;

    /**
     * @param  DataWrapperContract<TData>  $item
     * @param null|TFields $fields
     */
    abstract public function delete(DataWrapperContract $item, ?FieldsContract $fields = null): bool;

    /**
     * @param  DataWrapperContract<TData>  $item
     * @param null|TFields $fields
     *
     * @return DataWrapperContract<TData>
     */
    abstract public function save(DataWrapperContract $item, ?FieldsContract $fields = null): DataWrapperContract;

    public function isRecentlyCreated(): bool
    {
        return $this->isRecentlyCreated;
    }

    public function flushState(): void
    {
        $this->item = null;
        $this->itemID = null;
        $this->pages = null;
        $this->activePage = null;
    }

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            IndexPageContract::class,
            FormPageContract::class,
            DetailPageContract::class,
        ];
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    public function isCreateInModal(): bool
    {
        return $this->createInModal;
    }

    public function resolveCreateModal(ModalContract $modal): ModalContract
    {
        return $this->modifyCreateModal($modal);
    }

    protected function modifyCreateModal(ModalContract $modal): ModalContract
    {
        return $modal->wide();
    }

    public function isEditInModal(): bool
    {
        return $this->editInModal;
    }

    public function resolveEditModal(ModalContract $modal): ModalContract
    {
        return $this->modifyEditModal($modal);
    }

    protected function modifyEditModal(ModalContract $modal): ModalContract
    {
        return $modal->wide();
    }

    public function isDetailInModal(): bool
    {
        return $this->detailInModal;
    }

    public function resolveDetailModal(ModalContract $modal): ModalContract
    {
        return $this->modifyDetailModal($modal);
    }

    protected function modifyDetailModal(ModalContract $modal): ModalContract
    {
        return $modal->wide();
    }

    public function resolveDeleteModal(ModalContract $modal): ModalContract
    {
        return $this->modifyDeleteModal($modal);
    }

    protected function modifyDeleteModal(ModalContract $modal): ModalContract
    {
        return $modal->auto();
    }

    public function resolveMassDeleteModal(ModalContract $modal): ModalContract
    {
        return $this->modifyMassDeleteModal($modal);
    }

    protected function modifyMassDeleteModal(ModalContract $modal): ModalContract
    {
        return $modal->auto();
    }

    public function resolveFiltersOffCanvas(OffCanvasContract $offCanvas): OffCanvasContract
    {
        return $this->modifyFiltersOffCanvas($offCanvas);
    }

    protected function modifyFiltersOffCanvas(OffCanvasContract $offCanvas): OffCanvasContract
    {
        return $offCanvas;
    }

    /**
     * @return null|TIndexPage
     */
    public function getIndexPage(): ?PageContract
    {
        return $this->getPages()->indexPage();
    }

    public function isIndexPage(): bool
    {
        return $this->getActivePage() instanceof IndexPageContract;
    }

    /**
     * @return null|TFormPage
     */
    public function getFormPage(): ?PageContract
    {
        return $this->getPages()->formPage();
    }

    public function isFormPage(): bool
    {
        return $this->getActivePage() instanceof FormPageContract;
    }

    public function isCreateFormPage(): bool
    {
        return $this->isFormPage() && \is_null($this->getItemID());
    }

    public function isUpdateFormPage(): bool
    {
        return $this->isFormPage() && ! \is_null($this->getItemID());
    }

    public function setActivePage(?PageContract $page): void
    {
        $this->activePage = $page;
    }

    public function getActivePage(): ?PageContract
    {
        return $this->activePage ?? $this->getPages()->activePage();
    }

    /**
     * @return null|TDetailPage
     */
    public function getDetailPage(): ?PageContract
    {
        return $this->getPages()->detailPage();
    }

    public function isDetailPage(): bool
    {
        return $this->getActivePage() instanceof DetailPageContract;
    }

    public function getCaster(): DataCasterContract
    {
        return new MixedDataCaster($this->casterKeyName);
    }

    /**
     * @return DataWrapperContract<TData>|null
     */
    public function getCastedData(): ?DataWrapperContract
    {
        if (\is_null($this->getItem())) {
            return null;
        }

        return $this->getCaster()->cast(
            $this->getItem(),
        );
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


    public function isDeleteRelationships(): bool
    {
        return $this->deleteRelationships;
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

    /**
     * @return null|Closure(iterable<TData> $items, TableBuilderContract $table): iterable<TData>
     */
    public function getItemsResolver(): ?Closure
    {
        return null;
    }

    /**
     * @param  DataWrapperContract<TData>  $item
     */
    public function modifyResponse(DataWrapperContract $item): Jsonable
    {
        return $item->getOriginal();
    }

    /**
     * @param  iterable<TData>  $items
     */
    public function modifyCollectionResponse(mixed $items): Jsonable
    {
        return $items;
    }
}
