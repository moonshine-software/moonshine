<?php

declare(strict_types=1);

namespace MoonShine\Traits\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait WithRelatedValues
{
    protected array $values = [];

    protected ?Closure $valuesQuery = null;

    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    public function values(): array
    {
        return $this->values;
    }

    public function valuesQuery(Closure $callback): self
    {
        $this->valuesQuery = $callback;

        return $this;
    }

    public function relatedValues(Model $item): array
    {
        $related = $this->getRelated($item);
        $query = $related->newModelQuery();

        if (is_callable($this->valuesQuery)) {
            $query = call_user_func($this->valuesQuery, $query);
        }

        if (is_callable($this->valueCallback())) {
            $values = $query->get()
                ->mapWithKeys(function ($relatedItem) {
                    return [$relatedItem->getKey() => ($this->valueCallback())($relatedItem)];
                });
        } else {
            $values = $query->selectRaw($related->getTable().'.*')
                ->toBase()
                ->get()
                ->pluck($this->resourceTitleField(), $related->getKeyName());
            // Без этого ошибка MySQL потому что ->pluck берет только нужные поля, это изменение ни на что не повлияет
            // (ну почти. только на расход памяти, потому добавил ->toBase()):
            // SQLSTATE[23000]: Integrity constraint violation: 1052 Column 'id' in field list is ambiguous
            //SELECT
            //  `title`,
            //  `id`
            //FROM
            //  `categories`
            //  INNER JOIN `article_category` ON `categories`.`id` = `article_category`.`category_id`
            //WHERE
            //  `article_category`.`article_id` = 1

            // Без ->selectRaw($related->getTable().'.*') он берет ID из таблицы промежуточной (((
        }

        return $values->toArray();
    }
}
