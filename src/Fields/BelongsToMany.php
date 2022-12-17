<?php

declare(strict_types=1);

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Leeto\MoonShine\Contracts\Fields\HasFields;
use Leeto\MoonShine\Contracts\Fields\Relationships\HasRelationship;
use Leeto\MoonShine\Contracts\Fields\HasPivot;
use Leeto\MoonShine\Contracts\Fields\Relationships\ManyToManyRelation;
use Leeto\MoonShine\Traits\Fields\CheckboxTrait;
use Leeto\MoonShine\Traits\Fields\SelectTransform;
use Leeto\MoonShine\Traits\Fields\WithFields;
use Leeto\MoonShine\Traits\Fields\WithRelationship;
use Leeto\MoonShine\Traits\Fields\WithPivot;
use Leeto\MoonShine\Traits\Fields\Searchable;

class BelongsToMany extends Field implements HasRelationship, HasPivot, HasFields, ManyToManyRelation
{
    use WithFields;
    use WithPivot;
    use WithRelationship;
    use CheckboxTrait;
    use Searchable;
    use SelectTransform;

    public static string $view = 'moonshine::fields.belongs-to-many';

    protected bool $group = true;

    protected bool $tree = false;

    protected string $treeHtml = '';

    protected string $treeParentColumn = '';

    protected array $ids = [];

    protected bool $onlyCount = false;

    public function ids(): array
    {
        return $this->ids;
    }

    public function onlyCount(): static
    {
        $this->onlyCount = true;

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

        $this->treeHtml = (string) str($this->treeHtml())->wrap("<ul>", "</ul>");
    }

    public function buildTreeHtml(Model $item): string
    {
        $related = $this->getRelated($item);
        $query = $related->newModelQuery();

        if(is_callable($this->valuesQuery)) {
            $query = call_user_func($this->valuesQuery, $query);
        }

        $data = $query->get();

        $this->treePerformHtml($data);

        return $this->treeHtml();
    }

    private function makeTree(array $performedData, int $parent_id = 0, int $offset = 0): void
    {
        if (isset($performedData[$parent_id])) {
            foreach ($performedData[$parent_id] as $item) {
                $this->ids[] = $item->getKey();

                $element = view('moonshine::fields.shared.checkbox', [
                    'attributes' => $this->attributes(),
                    'id' => $this->id(),
                    'name' => $this->name(),
                    'value' => $item->getKey(),
                    'label' => $item->{$this->resourceTitleField()}
                ]);

                $this->treeHtml .= str($element)->wrap(
                    "<li x-ref='item_{$item->getKey()}'
                            style='margin-left: ".($offset * 50)."px'
                            class='mb-3 bg-whiteblue dark:bg-purple py-4 px-4 rounded-md'>",
                    "</li>"
                );

                $this->makeTree($performedData, $item->getKey(), $offset + 1);
            }
        }
    }

    public function indexViewValue(Model $item, bool $container = false): string
    {
        $result = str('');

        if($this->onlyCount) {
            return (string) $item->{$this->relation()}->count();
        }

        return (string) $item->{$this->relation()}->map(function ($item) use ($result) {
            $pivotAs = $this->getPivotAs($item);


            $result = $result->append($item->{$this->resourceTitleField()})
                ->when($this->hasFields(), fn(Stringable $str) => $str->append(' - '));

            foreach ($this->getFields() as $field) {
                $result = $result->when(
                    $field->formViewValue($item->{$pivotAs}),
                    function (Stringable $str) use ($pivotAs, $field, $item) {
                        return $str->append($field->formViewValue($item->{$pivotAs}));
                    }
                );
            }

            return (string) $result;
        })->implode(',');
    }

    public function save(Model $item): Model
    {
        $values = $this->requestValue() ? $this->requestValue() : [];
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

    public function exportViewValue(Model $item): mixed
    {
        return collect($item->{$this->relation()})
            ->map(fn($item) => $item->{$this->resourceTitleField()})
            ->implode(';');
    }
}
