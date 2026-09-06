<?php

declare(strict_types=1);

namespace MoonShine\Laravel\Traits\Fields;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use MoonShine\Support\Stringify;
use MoonShine\UI\Fields\Checkbox;
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
        /** @var Collection<array-key, Collection<array-key, \Illuminate\Database\Eloquent\Model>> $data */
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
     * @param Collection<array-key, Collection<array-key, \Illuminate\Database\Eloquent\Model>> $data
     */
    protected function buildTree(Collection $data, int|string $parentKey = 0, int $offset = 0): string
    {
        if ($data->has($parentKey)) {
            foreach ($data->get($parentKey) ?? [] as $item) {
                $label = $this->getColumnOrFormattedValue($item, Stringify::value(data_get($item, $this->getResourceColumn())));

                $element = Checkbox::make((string) $label)
                    ->formName($this->getFormName())
                    ->simpleMode()
                    ->customAttributes($this->getAttributes()->jsonSerialize())
                    ->customAttributes($this->getReactiveAttributes())
                    ->setNameAttribute($this->getNameAttribute(Stringify::value($item->getKey())))
                    ->setValue($item->getKey());

                $this->treeHtml .= Str::of((string) $element)->wrap(
                    "<li style='margin-left: " . ($offset * 30) . "px'>",
                    "</li>"
                );

                $this->buildTree($data, Stringify::value($item->getKey()), $offset + 1);
            }
        }

        return Str::of($this->treeHtml)->wrap(
            "<ul class='tree-list'>",
            "</ul>"
        )->value();
    }
}
