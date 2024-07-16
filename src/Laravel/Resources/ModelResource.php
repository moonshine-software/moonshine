<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Resources;

use Closure;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use MoonShine\Contracts\Core\CrudResourceContract;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Contracts\Core\TypeCasts\CastedDataContract;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Core\Exceptions\ResourceException;
use MoonShine\Core\Resources\Resource;
use MoonShine\Laravel\Fields\Relationships\ModelRelationField;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Traits\Resource\ResourceModelActions;
use MoonShine\Laravel\Traits\Resource\ResourceModelCrudRouter;
use MoonShine\Laravel\Traits\Resource\ResourceModelEvents;
use MoonShine\Laravel\Traits\Resource\ResourceModelPageComponents;
use MoonShine\Laravel\Traits\Resource\ResourceModelPolicy;
use MoonShine\Laravel\Traits\Resource\ResourceModelQuery;
use MoonShine\Laravel\Traits\Resource\ResourceModelValidation;
use MoonShine\Laravel\Traits\Resource\ResourceWithButtons;
use MoonShine\Laravel\Traits\Resource\ResourceWithFields;
use MoonShine\Laravel\TypeCasts\ModelCaster;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\Enums\ClickAction;
use MoonShine\Support\Enums\JsEvent;
use MoonShine\Laravel\Collections\Fields;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use Throwable;

/**
 * @template-covariant TModel of Model
 */
abstract class ModelResource extends Resource implements CrudResourceContract
{
    use ResourceWithFields;
    use ResourceWithButtons;

    /** @use ResourceModelValidation<TModel> */
    use ResourceModelValidation;
    use ResourceModelActions;
    use ResourceModelPolicy;

    /** @use ResourceModelQuery<TModel> */
    use ResourceModelQuery;

    /** @use ResourceModelCrudRouter<TModel> */
    use ResourceModelCrudRouter;

    /** @use ResourceModelEvents<TModel> */
    use ResourceModelEvents;
    use ResourceModelPageComponents;

    protected string $model;

    protected string $column = 'id';

    protected bool $createInModal = false;

    protected bool $editInModal = false;

    protected bool $detailInModal = false;

    protected bool $isAsync = true;

    protected bool $isPrecognitive = false;

    protected bool $deleteRelationships = false;

    /**
     * The click action to use when clicking on the resource in the table.
     */
    protected ?ClickAction $clickAction = null;

    protected bool $stickyTable = false;

    public function flushState(): void
    {
        $this->item = null;
        $this->itemID = null;
        $this->query = null;
        $this->pages = null;
    }

    /**
     * @return Metric
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

    /**
     * @return TModel
     */
    public function getModel(): Model
    {
        return new $this->model();
    }

    public function getModelCast(): ModelCaster
    {
        return new ModelCaster($this->model);
    }

    public function getCastedItem(): ?CastedDataContract
    {
        if(is_null($this->getItem())) {
            return null;
        }

        return $this->getModelCast()->cast($this->getItem());
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

    /**
     * @return list<Metric>
     */
    public function metrics(): array
    {
        return [];
    }

    public function trAttributes(): Closure
    {
        return static fn (?CastedDataContract $data, int $row): array => [];
    }

    public function tdAttributes(): Closure
    {
        return static fn (?CastedDataContract $data, int $row, int $cell): array => [];
    }

    /**
     * @return string[]
     */
    public function search(): array
    {
        return ['id'];
    }

    public function getListComponentName(): string
    {
        return rescue(
            fn (): string => $this->getIndexPage()?->getListComponentName(),
            'index-table',
            false
        );
    }

    public function getListEventName(?string $name = null): string
    {
        $name ??= $this->getListComponentName();

        return rescue(
            fn (): string => AlpineJs::event($this->getIndexPage()?->getListEventName() ?? '', $name),
            AlpineJs::event(JsEvent::TABLE_UPDATED, $name),
            false
        );
    }

    /**
     * @param  array<int|string>  $ids
     */
    public function massDelete(array $ids): void
    {
        $this->beforeMassDeleting($ids);

        $this->getModel()
            ->newModelQuery()
            ->whereIn($this->getModel()->getKeyName(), $ids)
            ->get()
            ->each(function (Model $item): ?bool {
                $item = $this->beforeDeleting($item);

                return tap($item->delete(), fn (): Model => $this->afterDeleted($item));
            });

        $this->afterMassDeleted($ids);
    }

    /**
     * @param TModel $item
     * @throws Throwable
     */
    public function delete(Model $item, ?Fields $fields = null): bool
    {
        $item = $this->beforeDeleting($item);

        $fields ??= $this->getFormFields()->onlyFields();

        $fields->fill($item->toArray(), $this->getModelCast()->cast($item));

        $fields->each(static fn (FieldContract $field): mixed => $field->afterDestroy($item));

        if ($this->isDeleteRelationships()) {
            $this->getOutsideFields()->each(static function (ModelRelationField $field) use ($item): void {
                $relationItems = $item->{$field->getRelationName()};

                ! $field->isToOne() ?: $relationItems = collect([$relationItems]);

                $relationItems->each(
                    static fn (Model $relationItem): mixed => $field->afterDestroy($relationItem)
                );
            });
        }

        return (bool) tap($item->delete(), fn (): Model => $this->afterDeleted($item));
    }

    public function onSave(FieldContract $field): Closure
    {
        /**
         * @param TModel $item
         * @return TModel
         */
        return static function (Model $item) use ($field): Model {
            if (! $field->hasRequestValue() && ! $field->getDefaultIfExists()) {
                return $item;
            }

            $value = $field->getRequestValue() !== false ? $field->getRequestValue() : null;

            data_set($item, $field->getColumn(), $value);

            return $item;
        };
    }

    /**
     * @param TModel $item
     * @return TModel
     *
     * @throws ResourceException
     * @throws Throwable
     */
    public function save(Model $item, ?Fields $fields = null): Model
    {
        $fields ??= $this->getFormFields()->onlyFields();

        $fields->fill($item->toArray(), $this->getModelCast()->cast($item));

        try {
            $fields->each(static fn (FieldContract $field): mixed => $field->beforeApply($item));

            if (! $item->exists) {
                $item = $this->beforeCreating($item);
            }

            if ($item->exists) {
                $item = $this->beforeUpdating($item);
            }

            $fields->withoutOutside()
                ->each(fn (FieldContract $field): mixed => $field->apply($this->onSave($field), $item));

            if ($item->save()) {
                $item = $this->afterSave($item, $fields);
            }
        } catch (QueryException $queryException) {
            throw new ResourceException($queryException->getMessage());
        }

        $this->setItem($item);

        return $item;
    }

    /**
     * @param TModel $item
     * @return TModel
     */
    private function afterSave(Model $item, Fields $fields): Model
    {
        $wasRecentlyCreated = $item->wasRecentlyCreated;

        $fields->each(static fn (FieldContract $field): mixed => $field->afterApply($item));

        if ($item->isDirty()) {
            $item->save();
        }

        if ($wasRecentlyCreated) {
            $item = $this->afterCreated($item);
        }

        if (! $wasRecentlyCreated) {
            $item = $this->afterUpdated($item);
        }

        return $item;
    }

    /**
     * @param  TModel  $item
     */
    public function prepareJsonResponse(Model $item): mixed
    {
        return $item;
    }

    /**
     * @param  iterable<TModel>  $items
     */
    public function prepareCollectionJsonResponse(Paginator $items): mixed
    {
        return $items;
    }
}
