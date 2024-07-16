<?php

declare(strict_types=1);

namespace MoonShine\UI\Collections;

use Closure;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Core\DependencyInjection\FieldsContract;
use MoonShine\UI\Components\Table\TableTd;
use MoonShine\UI\Fields\Td;

/**
 * @template TKey of array-key
 *
 * @extends Collection<TKey, TableTd>
 */
final class TableCells extends Collection
{
    public function pushFields(FieldsContract $fields, ?Closure $builder = null): self
    {
        $initializedBuilder = $builder;

        foreach ($fields as $index => $field) {
            if($field instanceof Td && $field->hasTdAttributes()) {
                $builder = static fn (TableTd $td): TableTd => $td->customAttributes(
                    $field->resolveTdAttributes($field->getData())
                );
            }

            $this->pushCell(
                (string) $field,
                $index,
                $builder ?? $initializedBuilder
            );

            $builder = null;
        }

        return $this;
    }

    public function pushCell(Closure|string $content, ?int $index = null, ?Closure $builder = null): self
    {
        return $this->push(
            TableTd::make($content, $index)->when(
                ! is_null($builder),
                static fn (TableTd $td) => $builder($td)
            )
        );
    }

    public function pushWhen(Closure|bool $condition, Closure $value): self
    {
        if(value($condition) === false) {
            return $this;
        }

        return $this->push(
            value($value)
        );
    }

    public function pushCellWhen(Closure|bool $condition, Closure|string $content, ?int $index = null, ?Closure $builder = null): self
    {
        if(value($condition) === false) {
            return $this;
        }

        return $this->pushCell($content, $index, $builder);
    }
}
