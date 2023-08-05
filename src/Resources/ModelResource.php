<?php

declare(strict_types=1);

namespace MoonShine\Resources;

use Closure;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use MoonShine\Exceptions\ResourceException;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Relationships\ModelRelationField;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Traits\Resource\ResourceModelCrudRouter;
use MoonShine\Traits\Resource\ResourceModelEvents;
use MoonShine\Traits\Resource\ResourceModelPolicy;
use MoonShine\Traits\Resource\ResourceModelQuery;
use Throwable;

abstract class ModelResource extends Resource
{
    use ResourceModelPolicy;
    use ResourceModelQuery;
    use ResourceModelCrudRouter;
    use ResourceModelEvents;

    protected string $model;

    protected string $title = '';

    protected string $column = 'id';

    protected ?Model $item = null;

    abstract public function fields(): array;

    /**
     * Get an array of validation rules for resource related model
     *
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    abstract public function rules(Model $item): array;

    public function pages(): array
    {
        return [
            IndexPage::make($this->title()),
            FormPage::make(
                $this->getItemID()
                    ? 'Редактировать'
                    : 'Добавить'
            ),
        ];
    }

    public function title(): string
    {
        return $this->title;
    }

    public function column(): string
    {
        return $this->column;
    }

    public function onSave(): Closure
    {
        return static function (Field $field, Model $item): Model {
            if ($field->requestValue()) {
                $item->{$field->column()} = $field->requestValue();
            }

            return $item;
        };
    }

    public function getFields(): Fields
    {
        return Fields::make($this->fields());
    }

    public function getActiveActions(): array
    {
        return ['edit'];
    }

    public function search(): array
    {
        return ['id'];
    }

    public function getModel(): Model
    {
        return new $this->model();
    }

    /**
     * Get custom messages for validator errors
     *
     * @return array<string, string|array<string, string>>
     */
    public function validationMessages(): array
    {
        return [];
    }

    /**
     * @throws Throwable
     */
    public function validate(Model $item): ValidatorContract
    {
        return Validator::make(
            moonshineRequest()->all(),
            $this->rules($item),
            array_merge(
                trans('moonshine::validation'),
                $this->validationMessages()
            ),
            $this->getFields()->extractLabels()
        );
    }

    public function prepareForValidation(): void
    {
    }

    public function getItemID(): int|string|null
    {
        return request('resourceItem');
    }

    public function getItem(): ?Model
    {
        if ($this->item instanceof Model) {
            return $this->item;
        }

        $this->item = $this->getModel()
            ->newQuery()
            ->find($this->getItemID());

        return $this->item;
    }

    public function getItemOrInstance(): Model
    {
        if ($this->item instanceof Model) {
            return $this->item;
        }

        $this->item = $this->getModel()
            ->newQuery()
            ->findOrNew($this->getItemID());

        return $this->item;
    }

    public function getItemOrFail(): Model
    {
        if ($this->item instanceof Model) {
            return $this->item;
        }

        $this->item = $this->getModel()
            ->newQuery()
            ->findOrFail($this->getItemID());

        return $this->item;
    }

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

    public function delete(Model $item): bool
    {
        $this->beforeDeleting($item);

        $this->getFields()->formFields()->each(fn (Field $field) => $field->afterDestroy($item));

        return tap($item->delete(), function () use ($item): void {
            $this->afterDeleted($item);
        });
    }

    /**
     * @throws ResourceException|Throwable
     */
    public function save(Model $item, ?Collection $fields = null): Model
    {
        $fields ??= $this->getFields()
            ->formFields()
            ->fillClonedValues($item->toArray(), $item);

        try {
            $fields->each(fn (Field $field) => $field->beforeApply($item));

            if (! $item->exists) {
                $item = $this->beforeCreating($item);
            }

            if ($item->exists) {
                $item = $this->beforeUpdating($item);
            }

            $fields->each(fn (Field $field) => $field->apply($this->onSave(), $item));

            if ($item->save()) {
                $wasRecentlyCreated = $item->wasRecentlyCreated;

                $fields->onlyRelationFields()
                    ->each(fn (ModelRelationField $field) => $field->apply($this->onSave(), $item));

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
