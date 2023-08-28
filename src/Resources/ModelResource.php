<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Pages\Crud\ShowPage;
use MoonShine\Traits\Resource\ResourceModelActions;
use MoonShine\Traits\Resource\ResourceModelCrudRouter;
use MoonShine\Traits\Resource\ResourceModelEvents;
use MoonShine\Traits\Resource\ResourceModelPolicy;
use MoonShine\Traits\Resource\ResourceModelQuery;
use MoonShine\Traits\Resource\ResourceModelValidation;
use MoonShine\Traits\Resource\ResourceWithFields;
use MoonShine\TypeCasts\ModelCast;
use Throwable;

abstract class ModelResource extends Resource
{
    use ResourceWithFields;
    use ResourceModelValidation;
    use ResourceModelActions;
    use ResourceModelPolicy;
    use ResourceModelQuery;
    use ResourceModelCrudRouter;
    use ResourceModelEvents;

    protected string $model;

    protected string $title = '';

    protected string $column = 'id';

    protected function pages(): array
    {
        return [
            IndexPage::make($this->title()),
            FormPage::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),
            ShowPage::make(__('moonshine::ui.show')),
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
            if ($field->requestValue()) {
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
        $fields ??= $this->getFields()
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

            $fields->withoutRelationFields()
                ->each(fn (Field $field): mixed => $field->apply($this->onSave($field), $item));

            if ($item->save()) {
                $wasRecentlyCreated = $item->wasRecentlyCreated;

                $fields->onlyRelationFields()
                    ->each(fn (ModelRelationField $field): mixed => $field->apply($this->onSave($field), $item));

                $item->save();

                $fields->each(fn (Field $field) => $field->afterApply($item));

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
