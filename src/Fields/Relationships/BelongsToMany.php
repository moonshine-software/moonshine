<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Components\TableBuilder;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasPivot;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Fields\Checkbox;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\ID;
use MoonShine\Fields\NoInput;
use MoonShine\Fields\Text;
use MoonShine\Traits\Fields\WithAsyncSearch;
use MoonShine\Traits\Fields\WithRelatedValues;
use MoonShine\Traits\WithFields;
use Throwable;

class BelongsToMany extends ModelRelationField implements
    HasRelatedValues,
    HasPivot,
    HasFields,
    HasAsyncSearch
{
    use WithFields;
    use WithRelatedValues;
    use WithAsyncSearch;

    protected string $view = 'moonshine::fields.relationships.belongs-to-many';

    protected bool $isGroup = true;

    protected bool $tree = false;

    protected string $treeHtml = '';

    protected string $treeParentColumn = '';

    protected bool $onlyCount = false;

    protected bool $inLine = false;

    protected string $inLineSeparator = '';

    protected bool $inLineBadge = false;

    public function getView(): string
    {
        if ($this->isTree()) {
            return 'moonshine::fields.shared.tree';
        }

        return parent::getView();
    }

    public function onlyCount(): static
    {
        $this->onlyCount = true;

        return $this;
    }

    public function inLine(string $separator = '', bool $badge = false): static
    {
        $this->inLine = true;
        $this->inLineSeparator = $separator;
        $this->inLineBadge = $badge;

        return $this;
    }

    public function tree(string $parentColumn): static
    {
        $this->treeParentColumn = $parentColumn;
        $this->tree = true;

        return $this;
    }

    protected function isTree(): bool
    {
        return $this->tree;
    }

    public function toTreeHtml(): string
    {
        $data = $this->resolveValuesQuery()
            ->get()
            ->map(fn ($item) => $item->setAttribute($this->treeParentColumn, $item->{$this->treeParentColumn} ?? 0))
            ->groupBy($this->treeParentColumn)
            ->map(fn ($items) => $items->keyBy($items->first()->getKeyName()));

        $this->treeHtml = '';

        return $this->buildTree($data);
    }

    protected function buildTree(Collection $data, int|string $parentKey = 0, int $offset = 0): string
    {
        if ($data->has($parentKey)) {
            foreach ($data->get($parentKey) as $item) {
                $element = view(
                    'moonshine::components.form.input-composition',
                    [
                        'attributes' => $this->attributes()->merge([
                            'type' => 'checkbox',
                            'id' => $this->id((string) $item->getKey()),
                            'name' => $this->name(),
                            'value' => $item->getKey(),
                            'class' => 'form-group-inline',
                        ]),
                        'beforeLabel' => true,
                        'label' => $item->{$this->getResourceColumn()},
                    ]
                );

                $this->treeHtml .= str($element)->wrap(
                    "<li style='margin-left: " . ($offset * 30) . "px'>",
                    "</li>"
                );

                $this->buildTree($data, $item->getKey(), $offset + 1);
            }
        }

        return str($this->treeHtml)->wrap(
            "<ul class='tree-list'>",
            "</ul>"
        )->value();
    }

    public function isChecked(string $value): bool
    {
        return collect($this->toValue())->has($value);
    }

    protected function getPivotAs(): string
    {
        return $this->getRelation()?->getPivotAccessor() ?? 'pivot';
    }

    protected function prepareFields(Fields $fields): Fields
    {
        if (! is_null($this->preparedFields)) {
            return $this->preparedFields;
        }

        if(is_null($this->getRelation())) {
            return $fields;
        }

        $this->preparedFields = $fields->map(fn (Field $field): Field => $field
            ->setColumn("{$this->getPivotAs()}.{$field->column()}")
            ->setName(
                "{$this->getRelationName()}_{$field->column()}[]"
            ));

        return $this->preparedFields;
    }

    protected function resolveValue(): mixed
    {
        $column = $this->getResourceColumn();

        $fields = $this->getFields()
            ->onlyFields()
            ->prepend(NoInput::make($column))
            ->prepend(Checkbox::make('#', '_checked'))
            ->toArray();

        $values = $this->resolveValuesQuery()->get();
        $values = $values->map(function ($value) {
            if($this->isChecked((string) $value->getKey())) {
                return collect($this->toValue())
                    ->firstWhere($value->getKeyName(), $value->getKey())
                    ->setAttribute('_checked', true);
            }

            return $value;
        });

        return TableBuilder::make(items: $values)
            ->fields($fields)
            ->cast($this->getModelCast())
            ->preview()
            ->editable()
            ->withNotFound();
    }

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): View|string
    {
        $values = $this->toValue() ?? [];
        $column = $this->getResourceColumn();

        if ($this->isRawMode()) {
            return $values
                ->map(fn (Model $item) => $item->{$column})
                ->implode(';');
        }

        if ($this->onlyCount) {
            return (string) $values->count();
        }

        if ($this->inLine) {
            return $values->implode(function (Model $item) use ($column) {
                $value = $item->{$column} ?? false;

                if (is_callable($this->formattedValueCallback())) {
                    $value = call_user_func(
                        $this->formattedValueCallback(),
                        $item
                    );
                }

                if ($this->inLineBadge) {
                    return view('moonshine::ui.badge', [
                        'color' => 'purple',
                        'value' => $value,
                        'margin' => true,
                    ])->render();
                }

                return $value;
            }, $this->inLineSeparator) ?? '';
        }

        # TODO Pivot values

        $fields = $this->getFields()
            ->indexFields()
            ->prepend(Text::make('#', $column))
            ->prepend(ID::make())
            ->toArray();

        return TableBuilder::make($fields, $values)
            ->preview()
            ->cast($this->getModelCast())
            ->render();
    }

    protected function resolveOnApply(): ?Closure
    {
        return function ($item) {
            $values = $this->requestValue() ?: [];
            $sync = [];

            foreach ($values as $index => $key) {
                # TODO[refactor] requestValues change to apply
                $sync[$key] = $this->getFields()
                    ->requestValues((string) $index)
                    ->toArray();
            }

            $item->{$this->getRelationName()}()->sync($sync);

            return $item;
        };
    }
}
