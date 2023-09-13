<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Traits\Resource\ResourceModelActions;
use MoonShine\Traits\Resource\ResourceModelCrudRouter;
use MoonShine\Traits\Resource\ResourceModelEvents;
use MoonShine\Traits\Resource\ResourceModelPolicy;
use MoonShine\Traits\Resource\ResourceModelQuery;
use MoonShine\Traits\Resource\ResourceModelValidation;
use MoonShine\Traits\Resource\ResourceWithButtons;
use MoonShine\Traits\Resource\ResourceWithFields;
use MoonShine\Traits\WithIsNowOnRoute;
use MoonShine\TypeCasts\ModelCast;
use Throwable;

abstract class ModelResource extends Resource
{
    use ResourceWithFields;
    use ResourceWithButtons;
    use ResourceModelValidation;
    use ResourceModelActions;
    use ResourceModelPolicy;
    use ResourceModelQuery;
    use ResourceModelCrudRouter;
    use ResourceModelEvents;
    use WithIsNowOnRoute;

    protected string $model;

    protected string $title = '';

    protected string $column = 'id';

    protected bool $createInModal = false;

    protected bool $editInModal = false;

    protected bool $detailInModal = false;

    protected bool $isAsync = false;

    protected bool $isPrecognitive = false;

    protected function pages(): array
    {
        return [
            IndexPage::make($this->title()),
            FormPage::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),
            DetailPage::make(__('moonshine::ui.show')),
        ];
    }

    public function getModel(): Model
    {
        return new $this->model();
    }

    public function getModelCast(): ModelCast
    {
        return ModelCast::make($this->model);
    }

    public function title(): string
    {
        return $this->title;
    }

    public function column(): string
    {
        return $this->column;
    }

    public function isCreateInModal(): bool
    {
        return $this->createInModal;
    }

    public function isEditInModal(): bool
    {
        return $this->createInModal;
    }

    public function isDetailInModal(): bool
    {
        return $this->createInModal;
    }

    public function isAsync(): bool
    {
        return $this->isAsync;
    }

    public function isPrecognitive(): bool
    {
        return $this->isPrecognitive;
    }

    public function metrics(): array
    {
        return [];
    }

    public function trAttributes(): ?Closure
    {
        return null;
    }

    public function tdAttributes(): ?Closure
    {
        return null;
    }

    public function search(): array
    {
        return ['id'];
    }

    /**
     * @param array<int|string> $ids
     */
    public function massDelete(array $ids): void
    {
        $this->beforeMassDeleting($ids);

        $this->getModel()
            ->newModelQuery()
            ->whereIn($this->getModel()->getKeyName(), $ids)
            ->get()
            ->each(fn (Model $item): ?bool => $item->delete());

        $this->afterMassDeleted($ids);
    }

    /**
     * @throws Throwable
     */
    public function delete(Model $item): bool
    {
        $item = $this->beforeDeleting($item);

        $this->getFields()
            ->onlyFields()
            ->each(fn (Field $field) => $field->afterDestroy($item));

        return tap($item->delete(), fn (): Model => $this->afterDeleted($item));
    }

    public function onSave(Field $field): Closure
    {
        return static function (Model $item) use ($field): Model {
            if ($field->requestValue() !== false) {
                data_set($item, $field->column(), $field->requestValue());
            }

            return $item;
        };
    }

    /**
     * @throws ResourceException|Throwable
     */
    public function save(Model $item, ?Fields $fields = null): Model
    {
        $fields ??= $this->getFormFields()
            ->onlyFields();

        $fields->fill($item->toArray(), $item);

        try {
            $fields->each(fn (Field $field) => $field->beforeApply($item));

            if (! $item->exists) {
                $item = $this->beforeCreating($item);
            }

            if ($item->exists) {
                $item = $this->beforeUpdating($item);
            }

            $fields->withoutOutside()
                ->each(fn (Field $field): mixed => $field->apply($this->onSave($field), $item));

            if ($item->save()) {
                $wasRecentlyCreated = $item->wasRecentlyCreated;

                $fields->each(fn (Field $field) => $field->afterApply($item));

                $item->save();

                if ($wasRecentlyCreated) {
                    $item = $this->afterCreated($item);
                }

                if (! $wasRecentlyCreated) {
                    $item = $this->afterUpdated($item);
                }
            }

        } catch (QueryException $queryException) {
            throw new ResourceException($queryException->getMessage());
        }

        return $item;
    }
}
