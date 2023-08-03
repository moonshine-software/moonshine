<?php

declare(strict_types=1);

namespace MoonShine\Fields\Relationships;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\HasPivot;
use MoonShine\Contracts\Fields\Relationships\HasAsyncSearch;
use MoonShine\Contracts\Fields\Relationships\HasRelatedValues;
use MoonShine\Fields\ID;
use MoonShine\Traits\Fields\CheckboxTrait;
use MoonShine\Traits\Fields\SelectTransform;
use MoonShine\Traits\Fields\WithAsyncSearch;
use MoonShine\Traits\Fields\WithPivot;
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
    use WithPivot;
    use WithRelatedValues;
    use CheckboxTrait;
    use SelectTransform;
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

    # TODO[refactor(tree)]
    public function tree(string $treeParentColumn): static
    {
        $this->treeParentColumn = $treeParentColumn;
        $this->tree = true;

        return $this;
    }

    public function isTree(): bool
    {
        return $this->tree;
    }

    public function buildTreeHtml(): string
    {
        $query = $this->getRelatedModel()->{$this->getRelation()}();
        $related = $query->getRelated();
        $query = $related->newModelQuery();

        if (is_callable($this->valuesQuery)) {
            $query = call_user_func($this->valuesQuery, $query);
        }

        $data = $query->get();

        $this->treePerformHtml($data);

        return $this->treeHtml();
    }

    private function treePerformHtml(Collection $data): void
    {
        $this->makeTree($this->treePerformData($data));

        $this->treeHtml = (string) str($this->treeHtml())->wrap(
            "<ul class='tree-list'>",
            "</ul>"
        );
    }

    private function makeTree(
        array $performedData,
        int|string $parent_id = 0,
        int $offset = 0
    ): void {
        if (isset($performedData[$parent_id])) {
            foreach ($performedData[$parent_id] as $item) {
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
                        'label' => $item->{$this->getResource()->column()},
                    ]
                );

                $this->treeHtml .= str($element)->wrap(
                    "<li style='margin-left: " . ($offset * 30) . "px'>",
                    "</li>"
                );

                $this->makeTree($performedData, $item->getKey(), $offset + 1);
            }
        }
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

    public function treeParentColumn(): string
    {
        return $this->treeParentColumn;
    }

    public function treeHtml(): string
    {
        return $this->treeHtml;
    }

    /**
     * @throws Throwable
     */
    protected function resolvePreview(): string
    {
        $values = $this->toValue();
        $column = $this->getResource()->column();

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

        $fields = $this->getFields()
            ->indexFields()
            ->prepend(ID::make())
            ->toArray();

        return (string) table($fields, $values);
    }
}
