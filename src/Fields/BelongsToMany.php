<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasPivot;
use MoonShine\Contracts\Fields\Relationships\HasRelationship;
use MoonShine\Contracts\Fields\Relationships\ManyToManyRelation;
use MoonShine\MoonShineRequest;
use MoonShine\Traits\Fields\CanBeMultiple;
use MoonShine\Traits\Fields\CheckboxTrait;
use MoonShine\Traits\Fields\Searchable;
use MoonShine\Traits\Fields\SelectTransform;
use MoonShine\Traits\Fields\WithPivot;
use MoonShine\Traits\Fields\WithRelationship;
use MoonShine\Traits\WithFields;

class BelongsToMany extends Field implements HasRelationship, HasPivot, HasFields, ManyToManyRelation
{
    use WithFields;
    use WithPivot;
    use WithRelationship;
    use CheckboxTrait;
    use Searchable;
    use SelectTransform;
    use CanBeMultiple;

    protected static string $view = 'moonshine::fields.belongs-to-many';

    protected bool $group = true;

    protected bool $tree = false;

    protected string $treeHtml = '';

    protected string $treeParentColumn = '';

    protected array $ids = [];

    protected bool $onlySelected = false;

    protected ?string $searchColumn = null;

    protected ?Closure $searchQuery = null;

    protected ?Closure $searchValueCallback = null;

    public function ids(): array
    {
        return $this->ids;
    }

    public function searchQuery(): ?Closure
    {
        return $this->searchQuery;
    }

    public function searchValueCallback(): ?Closure
    {
        return $this->searchValueCallback;
    }

    public function searchColumn(): ?string
    {
        return $this->searchColumn;
    }

    public function isOnlySelected(): bool
    {
        return $this->onlySelected;
    }

    public function onlySelected(string $relation, string $searchColumn = null, ?Closure $searchQuery = null, ?Closure $searchValueCallback = null): static
    {
        $this->onlySelected = true;
        $this->searchColumn = $searchColumn;
        $this->searchQuery = $searchQuery;
        $this->searchValueCallback = $searchValueCallback;

        $this->valuesQuery = function (Builder $query) use ($relation) {
            $request = app(MoonShineRequest::class);

            if ($request->getId()) {
                $related = $this->getRelated($request->getItem());
                $table = $related->{$relation}()->getRelated()->getTable();
                $key = $related->{$relation}()->getRelated()->getKeyName();

                return $query->whereRelation($relation, "$table.$key", '=', $request->getId());
            }

            return $query->has($relation, '>');
        };

        return $this;
    }

    public function treeHtml(): string
    {
        return $this->treeHtml;
    }

    public function tree(string $treeParentColumn): static
    {
        $this->treeParentColumn = $treeParentColumn;
        $this->tree = true;

        return $this;
    }

    public function treeParentColumn(): string
    {
        return $this->treeParentColumn;
    }

    public function isTree(): bool
    {
        return $this->tree;
    }

    private function treePerformData(Collection $data): array
    {
        $performData = [];

        foreach ($data as $item) {
            $parent = is_null($item->{$this->treeParentColumn()})
                ? 0
                : $item->{$this->treeParentColumn()};

            $performData[$parent][$item->getKey()] = $item;
        }

        return $performData;
    }

    private function treePerformHtml(Collection $data): void
    {
        $this->makeTree($this->treePerformData($data));

        $this->treeHtml = (string) str($this->treeHtml())->wrap("<ul class='tree-list'>", "</ul>");
    }

    public function buildTreeHtml(Model $item): string
    {
        $related = $this->getRelated($item);
        $query = $related->newModelQuery();

        if (is_callable($this->valuesQuery)) {
            $query = call_user_func($this->valuesQuery, $query);
        }

        $data = $query->get();

        $this->treePerformHtml($data);

        return $this->treeHtml();
    }

    private function makeTree(array $performedData, int|string $parent_id = 0, int $offset = 0): void
    {
        if (isset($performedData[$parent_id])) {
            foreach ($performedData[$parent_id] as $item) {
                $this->ids[] = $item->getKey();

                $element = view('moonshine::components.form.input-composition', [
                    'attributes' => $this->attributes()->merge([
                        'type' => 'checkbox',
                        'id' => $this->id((string) $item->getKey()),
                        'name' => $this->name(),
                        'value' => $item->getKey(),
                        'class' => 'form-group-inline',
                    ]),
                    'beforeLabel' => true,
                    'label' => $item->{$this->resourceTitleField()},
                ]);

                $this->treeHtml .= str($element)->wrap(
                    "<li x-ref='item_{$item->getKey()}'
                            style='margin-left: ".($offset * 30)."px'>",
                    "</li>"
                );

                $this->makeTree($performedData, $item->getKey(), $offset + 1);
            }
        }
    }

    public function indexViewValue(Model $item, bool $container = false): string
    {
        $result = str('');

        if ($this->onlyCount) {
            return (string) $item->{$this->relation()}->count();
        }

        return (string) $item->{$this->relation()}->map(function ($item) use ($result) {
            $pivotAs = $this->getPivotAs($item);

            $result = $result->append($item->{$this->resourceTitleField()})
                ->when($this->hasFields(), fn (Stringable $str) => $str->append(' - '));

            foreach ($this->getFields() as $field) {
                $result = $result->when(
                    $field->formViewValue($item->{$pivotAs}),
                    function (Stringable $str) use ($pivotAs, $field, $item) {
                        return $str->append($field->formViewValue($item->{$pivotAs}));
                    }
                );
            }

            return (string) $result;
        })->implode(', ');
    }

    public function save(Model $item): Model
    {
        $values = $this->requestValue() ?: [];
        $sync = [];

        if ($this->hasFields()) {
            foreach ($values as $index => $value) {
                foreach ($this->getFields() as $field) {
                    $sync[$value][$field->field()] = $field->requestValue()[$index] ?? '';
                }
            }
        } else {
            $sync = $values;
        }

        $item->{$this->relation()}()->sync($sync);

        return $item;
    }

    public function exportViewValue(Model $item): string
    {
        return collect($item->{$this->relation()})
            ->map(fn ($item) => $item->{$this->resourceTitleField()})
            ->implode(';');
    }
}
