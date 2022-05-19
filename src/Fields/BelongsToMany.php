<?php

namespace Leeto\MoonShine\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Leeto\MoonShine\Contracts\Fields\FieldHasFieldsContract;
use Leeto\MoonShine\Contracts\Fields\FieldHasRelationContract;
use Leeto\MoonShine\Contracts\Fields\FieldWithPivotContract;
use Leeto\MoonShine\Traits\Fields\FieldSelectTransformer;
use Leeto\MoonShine\Traits\Fields\FieldWithFieldsTrait;
use Leeto\MoonShine\Traits\Fields\FieldWithPivotTrait;
use Leeto\MoonShine\Traits\Fields\FieldWithRelationshipsTrait;
use Leeto\MoonShine\Traits\Fields\SearchableSelectFieldTrait;

class BelongsToMany extends BaseField implements FieldHasRelationContract, FieldWithPivotContract, FieldHasFieldsContract
{
    use FieldSelectTransformer, FieldWithRelationshipsTrait, FieldWithFieldsTrait, FieldWithPivotTrait;
    use SearchableSelectFieldTrait;

    public static string $view = 'belongs-to-many';

    protected bool $tree = false;

    protected string $treeHtml = '';

    protected string $treeParentColumn = '';

    protected array $ids = [];

    public function ids(): array
    {
        return $this->ids;
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

        $this->treeHtml = str($this->treeHtml())->wrap("<ul>", "</ul>");
    }

    public function buildTreeHtml(Model $item): string
    {
        $data = $item->{$this->relation()}()->getRelated()->all();

        $this->treePerformHtml($data);

        return $this->treeHtml();
    }

    private function makeTree(array $performedData, int $parent_id = 0, int $offset = 0): void
    {
        if(isset($performedData[$parent_id])) {
            foreach($performedData[$parent_id] as $item)
            {
                $this->ids[] = $item->getKey();

                $element = view('moonshine::fields.shared.checkbox', [
                    'meta' => $this->meta(),
                    'id' => $this->id(),
                    'name' => $this->name(),
                    'value' => $item->getKey(),
                    'label' => $item->{$this->resourceTitleField()}
                ]);

                $this->treeHtml .= str($element)->wrap(
                    "<li x-ref='item_{$item->getKey()}' style='margin-left: ".($offset*50)."px' class='mb-3 bg-purple py-4 px-4 rounded-md'>",
                    "</li>"
                );

                $this->makeTree($performedData, $item->getKey(), $offset + 1);
            }
        }
    }

    public function indexViewValue(Model $item, bool $container = false): string
    {
        $result = str('');

        return $item->{$this->relation()}->map(function ($item) use($result) {
            $pivotAs = $this->getPivotAs($item);


            $result = $result->append($item->{$this->resourceTitleField()})
                ->when($this->hasFields(), fn(Stringable $str) => $str->append(' - '));

            foreach ($this->getFields() as $field) {
                $result = $result->when($field->formViewValue($item->{$pivotAs}), function (Stringable $str) use($pivotAs, $field, $item) {
                    return $str->append($field->formViewValue($item->{$pivotAs}));
                });
            }

            return (string) $result;
        })->implode(',');
    }

    public function save(Model $item): Model
    {
        $values = $this->requestValue() ? $this->requestValue() : [];
        $sync = [];

        if($this->hasFields()) {
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