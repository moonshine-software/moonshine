<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Illuminate\Support\Collection;
use Throwable;

trait HasTreeMode
{
    protected bool $tree = false;

    protected string $treeHtml = '';

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

    /**
     * @throws Throwable
     */
    public function toTreeHtml(): string
    {
        $data = $this->resolveValuesQuery()
            ->get()
            ->groupBy($this->treeParentColumn)
            ->mapWithKeys(fn ($items, $key): array => [$key ?: 0 => $items->keyBy(
                $this->getRelation()?->getRelated()?->getKeyName() ?? 'id'
            )]);

        $this->treeHtml = '';

        return $this->buildTree($data);
    }

    /**
     * @throws Throwable
     */
    protected function buildTree(Collection $data, int|string $parentKey = 0, int $offset = 0): string
    {
        if ($data->has($parentKey)) {
            foreach ($data->get($parentKey) as $item) {
                $label = $this->columnOrFormattedValue($item, $item->{$this->getResourceColumn()});

                $element = view(
                    'moonshine::components.form.input-composition',
                    [
                        'attributes' => $this->attributes()->merge([
                            'type' => 'checkbox',
                            'id' => $this->id((string) $item->getKey()),
                            'name' => $this->name((string) $item->getKey()),
                            'value' => $item->getKey(),
                            'class' => 'form-group-inline',
                        ]),
                        'beforeLabel' => true,
                        'label' => $label,
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
}
